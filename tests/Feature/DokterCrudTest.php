<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_dokter(): void
    {
        $poli = Poli::factory()->create();

        $id = $this->postJson('/dokters', ['nama' => 'dr. Uji', 'poli_id' => $poli->id])
            ->assertCreated()
            ->json('data.id');

        $this->getJson('/dokters')->assertOk()->assertJsonPath('data.0.nama', 'dr. Uji');

        $this->putJson("/dokters/{$id}", ['nama' => 'dr. Uji Baru', 'poli_id' => $poli->id])
            ->assertOk()
            ->assertJsonPath('data.nama', 'dr. Uji Baru');

        $this->deleteJson("/dokters/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('dokters', ['id' => $id]);
    }

    public function test_crud_jadwal_dokter(): void
    {
        $dokter = Dokter::factory()->create();

        $id = $this->postJson('/jadwal-dokters', [
            'dokter_id' => $dokter->id,
            'hari' => 2,
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ])->assertCreated()->assertJsonPath('data.maks_pasien', 30)->json('data.id');

        $this->postJson('/jadwal-dokters', [
            'dokter_id' => $dokter->id,
            'hari' => 2,
            'jam_mulai' => '12:00',
            'jam_selesai' => '08:00',
        ])->assertUnprocessable()->assertJsonValidationErrors('jam_selesai');

        $this->putJson("/jadwal-dokters/{$id}", [
            'dokter_id' => $dokter->id,
            'hari' => 3,
            'jam_mulai' => '13:00',
            'jam_selesai' => '16:00',
            'maks_pasien' => 20,
        ])->assertOk()->assertJsonPath('data.hari', 3);

        $this->assertSame('13:00:00', JadwalDokter::find($id)->jam_mulai);

        $this->deleteJson("/jadwal-dokters/{$id}")->assertNoContent();
    }

    public function test_halaman_web_dapat_dibuka(): void
    {
        $this->seed();

        foreach (['/', '/antrean/berjalan', '/janji-temu', '/dokters', '/dokters/create', '/jadwal-dokters', '/jadwal-dokters/create'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
