<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear el usuario Administrador
        User::factory()->create([
            'nombres' => 'Usuario',
            'apellido_paterno' => 'Administrador',
            'apellido_materno' => '',
            'dni' => '11111111',
            'email' => 'admin@test.com',
            'rol' => 'Admin', // Sobrescribimos el rol por defecto del factory
        ]);

        // 2. Crear el usuario normal
        User::factory()->create([
            'nombres' => 'Usuario',
            'apellido_paterno' => 'de Prueba',
            'apellido_materno' => '',
            'dni' => '22222222',
            'email' => 'usuario@test.com',
            'rol' => 'Usuario', // Este ya es el rol por defecto, pero lo ponemos para ser claros
        ]);
    }
}
