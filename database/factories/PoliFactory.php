<?php

namespace Database\Factories;

use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poli>
 */
class PoliFactory extends Factory
{
    protected $model = Poli::class;

    public function definition(): array
    {
        return [
            'nama' => 'Poli '.fake()->unique()->word(),
            'kode' => strtoupper(fake()->unique()->lexify('???')),
        ];
    }
}
