<?php

namespace Tests\Feature;

use App\Models\JadwalDokter;
use App\Models\Pasien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JanjiTemuTest extends TestCase
{
    use RefreshDatabase;

    /** Senin, 16 November 2026. */
    private const SENIN = '2026-11-16';

    private JadwalDokter $jadwal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(self::SENIN.' 06:00:00');

        // Senin 08:00 - 12:00, kuota 30.
        $this->jadwal = JadwalDokter::factory()->create();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'jadwal_dokter_id' => $this->jadwal->id,
            'tanggal' => self::SENIN,
            'jam_mulai' => '08:00',
            'jam_selesai' => '08:15',
            'pasien_id' => Pasien::factory()->create()->id,
        ], $override);
    }

    public function test_janji_temu_dalam_jadwal_berhasil(): void
    {
        $this->postJson('/janji-temu', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.status', 'DIJADWALKAN')
            ->assertJsonPath('data.dokter_id', $this->jadwal->dokter_id);
    }

    /** Test #4: janji temu di luar jadwal dokter ditolak. */
    public function test_janji_temu_di_luar_jadwal_dokter_ditolak(): void
    {
        // Jam di luar sesi.
        $this->postJson('/janji-temu', $this->payload(['jam_mulai' => '13:00', 'jam_selesai' => '13:15']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jam_mulai');

        // Melewati jam selesai sesi.
        $this->postJson('/janji-temu', $this->payload(['jam_mulai' => '11:50', 'jam_selesai' => '12:10']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jam_mulai');

        // Hari yang bukan hari praktik (Selasa).
        $this->postJson('/janji-temu', $this->payload(['tanggal' => '2026-11-17']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tanggal');

        $this->assertDatabaseCount('janji_temus', 0);
    }

    /** Test #5: janji temu bentrok pada dokter dan slot sama ditolak. */
    public function test_janji_temu_bentrok_pada_slot_sama_ditolak(): void
    {
        $this->postJson('/janji-temu', $this->payload())->assertCreated();

        // Slot persis sama.
        $this->postJson('/janji-temu', $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jam_mulai');

        // Slot yang tumpang tindih sebagian.
        $this->postJson('/janji-temu', $this->payload(['jam_mulai' => '08:10', 'jam_selesai' => '08:25']))
            ->assertUnprocessable();

        // Slot berikutnya yang tidak tumpang tindih tetap boleh.
        $this->postJson('/janji-temu', $this->payload(['jam_mulai' => '08:15', 'jam_selesai' => '08:30']))
            ->assertCreated();

        $this->assertDatabaseCount('janji_temus', 2);
    }

    /** Test #6: pasien ke-31 pada satu sesi dokter ditolak. */
    public function test_pasien_ke_31_pada_satu_sesi_ditolak(): void
    {
        // Sesi panjang agar 31 slot 5 menit muat tanpa bentrok.
        $this->jadwal->update(['jam_mulai' => '07:00', 'jam_selesai' => '12:00', 'maks_pasien' => 30]);

        $mulai = Carbon::parse(self::SENIN.' 07:00');

        for ($i = 0; $i < 30; $i++) {
            $slot = $mulai->copy()->addMinutes($i * 5);
            $this->postJson('/janji-temu', $this->payload([
                'jam_mulai' => $slot->format('H:i'),
                'jam_selesai' => $slot->copy()->addMinutes(5)->format('H:i'),
            ]))->assertCreated();
        }

        $slot = $mulai->copy()->addMinutes(30 * 5);
        $this->postJson('/janji-temu', $this->payload([
            'jam_mulai' => $slot->format('H:i'),
            'jam_selesai' => $slot->copy()->addMinutes(5)->format('H:i'),
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jadwal_dokter_id');

        $this->assertDatabaseCount('janji_temus', 30);
    }

    public function test_antrean_yang_terikat_sesi_ikut_menghitung_kuota(): void
    {
        $this->jadwal->update(['maks_pasien' => 2]);
        $poliId = $this->jadwal->dokter->poli_id;

        $this->postJson('/antrean', ['poli_id' => $poliId, 'jadwal_dokter_id' => $this->jadwal->id])->assertCreated();
        $this->postJson('/janji-temu', $this->payload())->assertCreated();

        $this->postJson('/antrean', ['poli_id' => $poliId, 'jadwal_dokter_id' => $this->jadwal->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jadwal_dokter_id');
    }

    public function test_janji_temu_membuat_pasien_baru_dari_nama_dan_no_hp(): void
    {
        $payload = $this->payload(['nama' => 'Siti', 'no_hp' => '0812345678']);
        unset($payload['pasien_id']);

        $this->postJson('/janji-temu', $payload)->assertCreated();
        $this->assertDatabaseHas('pasiens', ['nama' => 'Siti', 'no_hp' => '0812345678']);
    }
}
