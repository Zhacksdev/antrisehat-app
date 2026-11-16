<?php

namespace Tests\Feature;

use App\Enums\StatusAntrean;
use App\Models\Antrean;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntreanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo('2026-11-16 08:00:00');
    }

    /** Test #2: nomor antrean berurutan per poli (1, 2, 3, ...). */
    public function test_nomor_antrean_berurutan_per_poli(): void
    {
        $umum = Poli::factory()->create();
        $gigi = Poli::factory()->create();

        $nomorUmum = collect(range(1, 3))
            ->map(fn () => $this->postJson('/antrean', ['poli_id' => $umum->id])->assertCreated()->json('data.nomor'))
            ->all();

        $this->assertSame([1, 2, 3], $nomorUmum);

        // Poli lain punya urutan sendiri.
        $this->postJson('/antrean', ['poli_id' => $gigi->id])
            ->assertCreated()
            ->assertJsonPath('data.nomor', 1);
    }

    /** Test #3: nomor antrean kembali ke 1 pada hari berikutnya. */
    public function test_nomor_antrean_kembali_ke_1_pada_hari_berikutnya(): void
    {
        $poli = Poli::factory()->create();

        $this->postJson('/antrean', ['poli_id' => $poli->id])->assertJsonPath('data.nomor', 1);
        $this->postJson('/antrean', ['poli_id' => $poli->id])->assertJsonPath('data.nomor', 2);

        $this->travelTo('2026-11-17 07:30:00');

        $this->postJson('/antrean', ['poli_id' => $poli->id])
            ->assertCreated()
            ->assertJsonPath('data.nomor', 1)
            ->assertJsonPath('data.tanggal', '2026-11-17');
    }

    /** Test #7: panggil antrean berikutnya mengubah status dengan benar. */
    public function test_panggil_antrean_berikutnya_mengubah_status(): void
    {
        $poli = Poli::factory()->create();
        $this->postJson('/antrean', ['poli_id' => $poli->id]);
        $this->postJson('/antrean', ['poli_id' => $poli->id]);

        $this->postJson('/antrean/panggil', ['poli_id' => $poli->id])
            ->assertOk()
            ->assertJsonPath('data.nomor', 1)
            ->assertJsonPath('data.status', StatusAntrean::Dipanggil->value);

        $this->postJson('/antrean/panggil', ['poli_id' => $poli->id])
            ->assertOk()
            ->assertJsonPath('data.nomor', 2);

        $this->assertSame(StatusAntrean::Selesai, Antrean::where('nomor', 1)->first()->status);
        $this->assertSame(StatusAntrean::Dipanggil, Antrean::where('nomor', 2)->first()->status);

        // Tidak ada lagi yang menunggu.
        $this->postJson('/antrean/panggil', ['poli_id' => $poli->id])->assertNotFound();
        $this->assertSame(StatusAntrean::Selesai, Antrean::where('nomor', 2)->first()->status);
    }

    public function test_tampilan_antrean_berjalan(): void
    {
        $poli = Poli::factory()->create(['nama' => 'Poli Umum', 'kode' => 'U']);
        $this->postJson('/antrean', ['poli_id' => $poli->id]);
        $this->postJson('/antrean', ['poli_id' => $poli->id]);
        $this->postJson('/antrean/panggil', ['poli_id' => $poli->id]);

        $this->getJson('/antrean/berjalan')
            ->assertOk()
            ->assertJsonPath('data.0.nomor_dipanggil', 1)
            ->assertJsonPath('data.0.nomor_berikutnya', 2)
            ->assertJsonPath('data.0.jumlah_menunggu', 1);

        $this->get('/antrean/berjalan')->assertOk()->assertSee('U-001');
    }

    public function test_ambil_antrean_lewat_form_web(): void
    {
        $poli = Poli::factory()->create(['kode' => 'G']);

        $this->post('/antrean', ['poli_id' => $poli->id, 'nama' => 'Budi', 'no_hp' => '0811111111'])
            ->assertRedirect(route('antrean.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('pasiens', ['no_hp' => '0811111111']);
        $this->assertDatabaseHas('antreans', ['poli_id' => $poli->id, 'nomor' => 1]);
    }
}
