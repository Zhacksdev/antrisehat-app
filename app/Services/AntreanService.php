<?php

namespace App\Services;

use App\Enums\StatusAntrean;
use App\Models\Antrean;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Poli;
use Illuminate\Support\Facades\DB;

class AntreanService
{
    public function __construct(private readonly KuotaSesiService $kuota) {}

    /**
     * Aturan bisnis #1: nomor berurutan per poli dan kembali ke 1 setiap hari.
     *
     * Nomor berikutnya = MAX(nomor) pada (poli, tanggal) + 1, dihitung di dalam
     * transaksi dengan lock pada baris poli. Unique constraint
     * (poli_id, tanggal, nomor) menjadi pengaman terakhir.
     */
    public function ambil(Poli $poli, ?Pasien $pasien = null, ?JadwalDokter $jadwal = null, ?string $tanggal = null): Antrean
    {
        $tanggal ??= today()->toDateString();

        return DB::transaction(function () use ($poli, $pasien, $jadwal, $tanggal) {
            Poli::query()->whereKey($poli->id)->lockForUpdate()->first();

            if ($jadwal !== null) {
                JadwalDokter::query()->whereKey($jadwal->id)->lockForUpdate()->first();
                $this->kuota->pastikanTersedia($jadwal, $tanggal);
            }

            $nomorTerakhir = (int) Antrean::query()
                ->where('poli_id', $poli->id)
                ->where('tanggal', $tanggal)
                ->max('nomor');

            return Antrean::create([
                'poli_id' => $poli->id,
                'jadwal_dokter_id' => $jadwal?->id,
                'pasien_id' => $pasien?->id,
                'tanggal' => $tanggal,
                'nomor' => $nomorTerakhir + 1,
                'status' => StatusAntrean::Menunggu,
            ])->load('poli');
        }, attempts: 3);
    }

    /**
     * Memanggil antrean berikutnya pada poli: antrean yang sedang DIPANGGIL
     * ditandai SELESAI, lalu antrean MENUNGGU dengan nomor terkecil menjadi DIPANGGIL.
     */
    public function panggilBerikutnya(Poli $poli, ?string $tanggal = null): ?Antrean
    {
        $tanggal ??= today()->toDateString();

        return DB::transaction(function () use ($poli, $tanggal) {
            Poli::query()->whereKey($poli->id)->lockForUpdate()->first();

            Antrean::query()
                ->where('poli_id', $poli->id)
                ->where('tanggal', $tanggal)
                ->where('status', StatusAntrean::Dipanggil->value)
                ->update(['status' => StatusAntrean::Selesai->value, 'updated_at' => now()]);

            $berikutnya = Antrean::query()
                ->where('poli_id', $poli->id)
                ->where('tanggal', $tanggal)
                ->where('status', StatusAntrean::Menunggu->value)
                ->orderBy('nomor')
                ->first();

            $berikutnya?->update([
                'status' => StatusAntrean::Dipanggil,
                'dipanggil_at' => now(),
            ]);

            return $berikutnya?->load('poli');
        }, attempts: 3);
    }

    /**
     * Ringkasan antrean berjalan per poli untuk tampilan publik.
     *
     * @return array<int, array<string, mixed>>
     */
    public function ringkasanBerjalan(?string $tanggal = null): array
    {
        $tanggal ??= today()->toDateString();

        return Poli::query()->orderBy('nama')->get()->map(function (Poli $poli) use ($tanggal) {
            $hariIni = Antrean::query()->where('poli_id', $poli->id)->where('tanggal', $tanggal);

            $dipanggil = (clone $hariIni)->where('status', StatusAntrean::Dipanggil->value)->orderByDesc('nomor')->first();
            $berikutnya = (clone $hariIni)->where('status', StatusAntrean::Menunggu->value)->orderBy('nomor')->first();

            return [
                'poli_id' => $poli->id,
                'poli' => $poli->nama,
                'kode' => $poli->kode,
                'nomor_dipanggil' => $dipanggil?->nomor,
                'nomor_berikutnya' => $berikutnya?->nomor,
                'jumlah_menunggu' => (clone $hariIni)->where('status', StatusAntrean::Menunggu->value)->count(),
                'total_hari_ini' => (clone $hariIni)->count(),
            ];
        })->all();
    }
}
