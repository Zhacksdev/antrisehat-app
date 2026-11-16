<?php

namespace App\Services;

use App\Enums\StatusJanjiTemu;
use App\Models\JadwalDokter;
use App\Models\JanjiTemu;
use App\Models\Pasien;
use App\Support\Jam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JanjiTemuService
{
    public function __construct(private readonly KuotaSesiService $kuota) {}

    /**
     * Aturan bisnis #2: janji temu hanya pada slot jadwal dokter dan tidak boleh bentrok.
     * Aturan bisnis #3: maksimal 30 pasien per sesi dokter.
     *
     * @throws ValidationException
     */
    public function buat(JadwalDokter $jadwal, Pasien $pasien, string $tanggal, string $jamMulai, ?string $jamSelesai = null): JanjiTemu
    {
        $tanggal = Carbon::parse($tanggal)->toDateString();
        $jamMulai = Jam::normalisasi($jamMulai);
        $jamSelesai = $jamSelesai
            ? Jam::normalisasi($jamSelesai)
            : Carbon::parse($jamMulai)->addMinutes((int) config('antrisehat.durasi_slot_menit', 15))->format('H:i:s');

        $this->validasiSlot($jadwal, $tanggal, $jamMulai, $jamSelesai);

        return DB::transaction(function () use ($jadwal, $pasien, $tanggal, $jamMulai, $jamSelesai) {
            // Serialisasi pemesanan per sesi agar cek bentrok & kuota konsisten.
            JadwalDokter::query()->whereKey($jadwal->id)->lockForUpdate()->first();

            $bentrok = JanjiTemu::query()
                ->where('dokter_id', $jadwal->dokter_id)
                ->where('tanggal', $tanggal)
                ->whereIn('status', StatusJanjiTemu::aktif())
                ->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai)
                ->exists();

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Slot jadwal dokter tersebut sudah dipesan (bentrok).',
                ]);
            }

            $this->kuota->pastikanTersedia($jadwal, $tanggal);

            return JanjiTemu::create([
                'dokter_id' => $jadwal->dokter_id,
                'jadwal_dokter_id' => $jadwal->id,
                'pasien_id' => $pasien->id,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'status' => StatusJanjiTemu::Dijadwalkan,
            ]);
        }, attempts: 3);
    }

    /**
     * @throws ValidationException
     */
    private function validasiSlot(JadwalDokter $jadwal, string $tanggal, string $jamMulai, string $jamSelesai): void
    {
        if (Carbon::parse($tanggal)->lt(today())) {
            throw ValidationException::withMessages(['tanggal' => 'Tanggal janji temu tidak boleh di masa lalu.']);
        }

        if (Carbon::parse($tanggal)->dayOfWeekIso !== $jadwal->hari) {
            throw ValidationException::withMessages([
                'tanggal' => "Dokter tidak praktik pada tanggal tersebut (jadwal: {$jadwal->label}).",
            ]);
        }

        $jadwalMulai = Jam::normalisasi($jadwal->jam_mulai);
        $jadwalSelesai = Jam::normalisasi($jadwal->jam_selesai);

        if ($jamMulai >= $jamSelesai || $jamMulai < $jadwalMulai || $jamSelesai > $jadwalSelesai) {
            throw ValidationException::withMessages([
                'jam_mulai' => "Jam janji temu di luar jadwal praktik dokter ({$jadwal->label}).",
            ]);
        }
    }
}
