<?php

namespace Database\Factories;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalDokter>
 */
class JadwalDokterFactory extends Factory
{
    protected $model = JadwalDokter::class;

    public function definition(): array
    {
        return [
            'dokter_id' => Dokter::factory(),
            'hari' => 1,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'maks_pasien' => 30,
        ];
    }
}
