<?php

namespace Database\Factories;

use App\Models\Dokter;
use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokter>
 */
class DokterFactory extends Factory
{
    protected $model = Dokter::class;

    public function definition(): array
    {
        return [
            'nama' => 'dr. '.fake()->name(),
            'poli_id' => Poli::factory(),
        ];
    }
}
