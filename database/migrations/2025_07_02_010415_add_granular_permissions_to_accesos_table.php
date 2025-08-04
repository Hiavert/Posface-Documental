<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accesos', function (Blueprint $table) {
            // Agregar nuevos campos de permisos granulares
            $table->boolean('permiso_ver')->default(false)->after('permiso');
            $table->boolean('permiso_editar')->default(false)->after('permiso_ver');
            $table->boolean('permiso_agregar')->default(false)->after('permiso_editar');
            $table->boolean('permiso_eliminar')->default(false)->after('permiso_agregar');
        });

        // Migrar datos existentes: si permiso = true, entonces permiso_ver = true
        DB::statement("UPDATE accesos SET permiso_ver = permiso WHERE permiso = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accesos', function (Blueprint $table) {
            $table->dropColumn([
                'permiso_ver',
                'permiso_editar', 
                'permiso_agregar',
                'permiso_eliminar'
            ]);
        });
    }
};
