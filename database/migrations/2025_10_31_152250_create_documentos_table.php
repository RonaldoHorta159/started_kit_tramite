<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            // `parent_id` BIGINT UNSIGNED NULL
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('documentos') // Se referencia a sí misma
                ->onDelete('set null')
                ->comment('Para respuestas (documento padre)');

            $table->string('codigo_unico', 20)->unique();
            $table->string('nro_documento', 50)->comment('Correlativo visible: 0001-YYYY');

            // Columnas de correlativos
            $table->unsignedInteger('correlativo_area')->default(0)->comment('Correlativo por AREA (legado)');
            $table->unsignedInteger('correlativo_tipo')->comment('Correlativo por TIPO+ANIO');

            $table->text('asunto');
            $table->unsignedInteger('folios');
            $table->enum('prioridad', ['Normal', 'Urgente'])->default('Normal');
            $table->string('archivo_path', 255)->nullable()->comment('Adjunto opcional (disco privado)');

            // Claves Foráneas
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que CREÓ el documento');
            $table->foreignId('area_origen_id')->constrained('areas')->comment('Área que emite');
            $table->foreignId('area_actual_id')->constrained('areas')->comment('Área que lo tiene AHORA');

            $table->enum('estado', ['Recibido', 'En Trámite', 'Archivado', 'Atendido', 'Rechazado']);

            $table->timestamps();

            // Columna generada: `anio_creacion` YEAR AS (YEAR(`created_at`)) STORED
            $table->year('anio_creacion')->storedAs(DB::raw('YEAR(created_at)'))->comment('Columna generada para índice por año');

            // Índices
            $table->index(['area_actual_id', 'estado'], 'idx_documentos_bandeja');
            $table->index('user_id', 'idx_documentos_creador');
            $table->index('created_at', 'idx_documentos_fecha_creacion');

            // UNIQUE KEY `documentos_tipo_correlativo_anio_unique` (`tipo_documento_id`,`correlativo_tipo`,`anio_creacion`)
            $table->unique(['tipo_documento_id', 'correlativo_tipo', 'anio_creacion'], 'documentos_tipo_correlativo_anio_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
