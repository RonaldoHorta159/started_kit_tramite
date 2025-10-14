<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'dni' => $this->faker->unique()->numerify('########'),
            'nombres' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'celular' => $this->faker->numerify('9########'),
            'password' => static::$password ??= Hash::make('password'),
            'foto_path' => null,
            'primary_area_id' => null, // 👈 Se establece explícitamente como nulo
            'rol' => 'Usuario', // 👈 Por defecto, creará usuarios normales
            'estado' => 'Activo',
            'remember_token' => Str::random(10),
        ];
    }
}
