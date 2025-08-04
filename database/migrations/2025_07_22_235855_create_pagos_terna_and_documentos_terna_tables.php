<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pagos_terna')) {
            Schema::create('pagos_terna', function (Blueprint $table) {
                $table->id();
                $table->string('codigo')->unique();
                $table->text('descripcion')->nullable();
                $table->enum('estado', ['iniciado', 'en_revision', 'pendiente_pago', 'pagado', 'cancelado'])->default('iniciado');
                $table->date('fecha_defensa');
                $table->dateTime('fecha_envio_admin')->nullable();
                $table->dateTime('fecha_envio_asistente')->nullable();
                $table->dateTime('fecha_pago')->nullable();

                $table->unsignedInteger('id_administrador');
                $table->unsignedInteger('id_asistente');

                $table->foreign('id_administrador')->references('id_usuario')->on('usuario')->onDelete('cascade');
                $table->foreign('id_asistente')->references('id_usuario')->on('usuario')->onDelete('cascade');

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('documentos_terna')) {
            Schema::create('documentos_terna', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pago_terna_id');
                $table->foreign('pago_terna_id')->references('id')->on('pagos_terna')->onDelete('cascade');

                $table->enum('tipo', [
                    'documento_fisico', 
                    'solvencia_cobranza', 
                    'acta_graduacion',
                    'constancia_participacion',
                    'orden_pago',
                    'propuesta_maestria'
                ]);
                $table->string('ruta_archivo');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_terna');
        Schema::dropIfExists('pagos_terna');
    }
};
