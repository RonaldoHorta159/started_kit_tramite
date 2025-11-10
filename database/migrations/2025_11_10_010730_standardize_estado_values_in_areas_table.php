<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('areas')->where('estado', 'ACTIVO')->update(['estado' => 'Activo']);
        DB::table('areas')->where('estado', 'INACTIVO')->update(['estado' => 'Inactivo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('areas')->where('estado', 'Activo')->update(['estado' => 'ACTIVO']);
        DB::table('areas')->where('estado', 'Inactivo')->update(['estado' => 'INACTIVO']);
    }
};
