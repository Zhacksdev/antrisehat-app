<?php

namespace Database\Factories;

use App\Models\Pasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pasien>
 */
class PasienFactory extends Factory
{
    protected $model = Pasien::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'no_hp' => fake()->unique()->numerify('08##########'),
        ];
    }
}
