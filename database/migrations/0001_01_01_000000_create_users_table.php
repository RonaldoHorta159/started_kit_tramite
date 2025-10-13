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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // --- CAMPOS MODIFICADOS Y AÑADIDOS ---
            $table->string('dni', 8)->unique();
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('email')->unique();
            $table->string('celular', 15)->nullable();
            $table->string('password');
            $table->string('foto_path')->nullable();
            $table->unsignedBigInteger('primary_area_id')->nullable(); // Clave foránea se añadirá después
            $table->enum('rol', ['Admin', 'Usuario', 'Mesa de Partes']);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            // --- FIN DE CAMPOS MODIFICADOS ---
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
