<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('documento_id')
                ->constrained('documentos')
                ->onDelete('cascade'); // Si se borra el documento, se borra su historial

            $table->foreignId('area_origen_id')->constrained('areas')->comment('Área que envía');
            $table->foreignId('area_destino_id')->constrained('areas')->comment('Área que recibe');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realiza el movimiento');

            $table->text('proveido')->nullable()->comment('Decreto o nota de derivación');
            $table->enum('estado', ['Derivado', 'Atendido', 'Rechazado']);
            $table->string('archivo_adjunto_path', 255)->nullable()->comment('Adjunto opcional en la derivación/respuesta');

            $table->timestamps();

            // Índices
            $table->index('documento_id', 'idx_movimientos_historial');
            $table->index('area_destino_id', 'idx_movimientos_area_destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
