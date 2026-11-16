<?php

namespace App\Services;

use App\Enums\StatusAntrean;
use App\Enums\StatusJanjiTemu;
use App\Models\Antrean;
use App\Models\JadwalDokter;
use App\Models\JanjiTemu;
use Illuminate\Validation\ValidationException;

/**
 * Aturan bisnis #3: maksimal N pasien (default 30) per sesi dokter per tanggal.
 * Kuota dihitung dari janji temu aktif + antrean yang terikat ke sesi tersebut.
 */
class KuotaSesiService
{
    public function terpakai(JadwalDokter $jadwal, string $tanggal): int
    {
        $janjiTemu = JanjiTemu::query()
            ->where('jadwal_dokter_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->whereIn('status', StatusJanjiTemu::aktif())
            ->count();

        $antrean = Antrean::query()
            ->where('jadwal_dokter_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->where('status', '!=', StatusAntrean::Kedaluwarsa->value)
            ->count();

        return $janjiTemu + $antrean;
    }

    public function batas(JadwalDokter $jadwal): int
    {
        return $jadwal->maks_pasien ?: (int) config('antrisehat.maks_pasien_per_sesi', 30);
    }

    /**
     * @throws ValidationException
     */
    public function pastikanTersedia(JadwalDokter $jadwal, string $tanggal): void
    {
        if ($this->terpakai($jadwal, $tanggal) >= $this->batas($jadwal)) {
            throw ValidationException::withMessages([
                'jadwal_dokter_id' => sprintf(
                    'Kuota sesi dokter sudah penuh (maksimal %d pasien per sesi).',
                    $this->batas($jadwal),
                ),
            ]);
        }
    }
}
