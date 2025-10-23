<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Add FK users.primary_area_id -> areas.id
     */
    public function up(): void
    {
        // (Opcional pero recomendado) Saneamos referencias inválidas antes de crear la FK
        // Evita que falle la migración si hay users con primary_area_id que no existe en areas
        DB::statement("
            UPDATE users u
            LEFT JOIN areas a ON a.id = u.primary_area_id
            SET u.primary_area_id = NULL
            WHERE u.primary_area_id IS NOT NULL AND a.id IS NULL
        ");

        // Añadimos índice (recomendado) y la FK
        Schema::table('users', function (Blueprint $table) {
            // Si ya existe el índice, MySQL lanzará error de duplicado.
            // Usa un nombre fijo para poder revertirlo en down().
            $table->index('primary_area_id', 'users_primary_area_id_index');

            // FK con ON DELETE SET NULL y ON UPDATE CASCADE
            $table->foreign('primary_area_id', 'users_primary_area_id_foreign')
                ->references('id')->on('areas')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Drop FK and index
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // El orden importa: primero se elimina la FK y luego el índice
            $table->dropForeign('users_primary_area_id_foreign');
            $table->dropIndex('users_primary_area_id_index');
        });
    }
};
