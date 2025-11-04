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
        Schema::create('correlativos_tipos_anuales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_documento_id')
                ->constrained('tipos_documento')
                ->onDelete('cascade');

            $table->year('anio');
            $table->unsignedInteger('ultimo_correlativo')->default(0);

            $table->timestamps();

            // UNIQUE KEY `corr_tipo_anio_unique` (`tipo_documento_id`,`anio`)
            $table->unique(['tipo_documento_id', 'anio'], 'corr_tipo_anio_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correlativos_tipos_anuales');
    }
};
