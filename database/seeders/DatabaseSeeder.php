<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Poli;
use Illuminate\Database\Seeder;

/**
 * Seeder minimal (PRD 3): 3 poli (Umum, Gigi, Anak), 5 dokter dengan jadwal,
 * beberapa pasien contoh. Idempoten: aman dijalankan ulang di pipeline.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $polis = collect([
            ['kode' => 'U', 'nama' => 'Poli Umum'],
            ['kode' => 'G', 'nama' => 'Poli Gigi'],
            ['kode' => 'A', 'nama' => 'Poli Anak'],
        ])->mapWithKeys(fn ($p) => [$p['kode'] => Poli::updateOrCreate(['kode' => $p['kode']], $p)]);

        $dokters = [
            ['nama' => 'dr. Andi Pratama', 'poli' => 'U', 'jadwal' => [[1, '08:00', '12:00'], [3, '08:00', '12:00'], [5, '13:00', '17:00']]],
            ['nama' => 'dr. Sari Wulandari', 'poli' => 'U', 'jadwal' => [[2, '08:00', '12:00'], [4, '13:00', '17:00'], [6, '08:00', '12:00']]],
            ['nama' => 'drg. Budi Santoso', 'poli' => 'G', 'jadwal' => [[1, '13:00', '17:00'], [4, '08:00', '12:00']]],
            ['nama' => 'drg. Maya Lestari', 'poli' => 'G', 'jadwal' => [[2, '13:00', '17:00'], [5, '08:00', '12:00']]],
            ['nama' => 'dr. Rina Hapsari, Sp.A', 'poli' => 'A', 'jadwal' => [[1, '09:00', '13:00'], [3, '13:00', '17:00'], [6, '09:00', '12:00']]],
        ];

        foreach ($dokters as $data) {
            $dokter = Dokter::updateOrCreate(
                ['nama' => $data['nama']],
                ['poli_id' => $polis[$data['poli']]->id],
            );

            foreach ($data['jadwal'] as [$hari, $mulai, $selesai]) {
                JadwalDokter::updateOrCreate(
                    ['dokter_id' => $dokter->id, 'hari' => $hari, 'jam_mulai' => "{$mulai}:00"],
                    ['jam_selesai' => "{$selesai}:00", 'maks_pasien' => 30],
                );
            }
        }

        foreach ([
            ['nama' => 'Ahmad Fauzi', 'no_hp' => '081200000001'],
            ['nama' => 'Dewi Anggraini', 'no_hp' => '081200000002'],
            ['nama' => 'Rizky Ramadhan', 'no_hp' => '081200000003'],
            ['nama' => 'Putri Maharani', 'no_hp' => '081200000004'],
        ] as $pasien) {
            Pasien::updateOrCreate(['no_hp' => $pasien['no_hp']], $pasien);
        }
    }
}
