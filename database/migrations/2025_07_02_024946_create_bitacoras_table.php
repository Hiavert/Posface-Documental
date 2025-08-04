<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Usuario que hizo el cambio
            $table->string('usuario_nombre')->nullable(); // Nombre del usuario
            $table->string('accion'); // crear, editar, eliminar, etc.
            $table->string('modulo'); // Módulo o modelo afectado
            $table->unsignedBigInteger('registro_id')->nullable(); // ID del registro afectado
            $table->json('datos_antes')->nullable(); // Datos antes del cambio
            $table->json('datos_despues')->nullable(); // Datos después del cambio
            $table->string('ip')->nullable(); // IP del usuario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
