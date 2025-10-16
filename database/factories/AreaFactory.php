<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => 'Oficina de ' . $this->faker->jobTitle(),
            'codigo' => $this->faker->unique()->regexify('[A-Z]{5}'),

            // --- CAMBIO AÑADIDO ---
            // Elige aleatoriamente uno de los dos valores del array.
            'estado' => $this->faker->randomElement(['ACTIVO', 'INACTIVO']),
        ];
    }
}
