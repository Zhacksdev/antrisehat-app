<?php

namespace Tests\Feature;

use App\Enums\StatusAntrean;
use App\Models\Antrean;
use App\Models\Poli;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetAntreanCommandTest extends TestCase
{
    use RefreshDatabase;

    /** Test #8: command antrean:reset menandai antrean lama. */
    public function test_command_reset_menandai_antrean_lama_kedaluwarsa(): void
    {
        $this->travelTo('2026-11-17 00:00:05');
        $poli = Poli::factory()->create();

        $buat = fn (string $tanggal, int $nomor, StatusAntrean $status) => Antrean::create([
            'poli_id' => $poli->id,
            'tanggal' => $tanggal,
            'nomor' => $nomor,
            'status' => $status,
        ]);

        $lamaMenunggu = $buat('2026-11-16', 1, StatusAntrean::Menunggu);
        $lamaDipanggil = $buat('2026-11-16', 2, StatusAntrean::Dipanggil);
        $lamaSelesai = $buat('2026-11-16', 3, StatusAntrean::Selesai);
        $hariIni = $buat('2026-11-17', 1, StatusAntrean::Menunggu);

        $this->artisan('antrean:reset')
            ->expectsOutputToContain('kedaluwarsa=1 sesi_ditutup=1')
            ->assertSuccessful();

        $this->assertSame(StatusAntrean::Kedaluwarsa, $lamaMenunggu->fresh()->status);
        $this->assertSame(StatusAntrean::Selesai, $lamaDipanggil->fresh()->status);
        $this->assertSame(StatusAntrean::Selesai, $lamaSelesai->fresh()->status);
        $this->assertSame(StatusAntrean::Menunggu, $hariIni->fresh()->status, 'Antrean hari ini tidak boleh tersentuh.');
    }

    public function test_command_reset_terdaftar_di_scheduler(): void
    {
        $events = collect(app(Schedule::class)->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'antrean:reset'));

        $this->assertCount(1, $events);
        $this->assertSame('0 0 * * *', $events->first()->expression);
    }
}
