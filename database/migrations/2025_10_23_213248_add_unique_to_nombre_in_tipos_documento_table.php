<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Agrega la restricción 'unique' a la columna 'nombre'.
     */
    public function up(): void
    {
        Schema::table('tipos_documento', function (Blueprint $table) {
            // Esta es la línea que querías agregar
            $table->unique('nombre');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la restricción 'unique'.
     */
    public function down(): void
    {
        Schema::table('tipos_documento', function (Blueprint $table) {
            // Esto revierte el cambio de 'up()'
            $table->dropUnique(['nombre']);
        });
    }
};
