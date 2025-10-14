<?php

namespace Database\Seeders;

use App\Models\Area; // ¡No olvides importar tu modelo!
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Llama al AreaFactory para crear 20 áreas falsas y guardarlas en la base de datos.
        Area::factory()->count(20)->create();
    }
}
