<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera un nombre de departamento falso, como "Oficina de Ventas" o "Departamento de Marketing".
            'nombre' => 'Oficina de ' . $this->faker->jobTitle(),

            // Genera un código único de 5 letras mayúsculas, ej: "RTYUI".
            'codigo' => $this->faker->unique()->regexify('[A-Z]{5}'),

            // No necesitamos definir 'estado' porque tu migración ya lo establece en 'ACTIVO' por defecto.
        ];
    }
}
