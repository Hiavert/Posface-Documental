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
            // Si existe permiso_ver y permiso, migrar los datos y eliminar permiso_ver
            if (Schema::hasColumn('accesos', 'permiso_ver') && Schema::hasColumn('accesos', 'permiso')) {
                // Migrar datos de permiso_ver a permiso
                DB::statement("UPDATE accesos SET permiso = permiso_ver WHERE permiso IS NULL OR permiso = 0");
                
                // Eliminar la columna permiso_ver
                $table->dropColumn('permiso_ver');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accesos', function (Blueprint $table) {
            // Restaurar permiso_ver si no existe
            if (!Schema::hasColumn('accesos', 'permiso_ver')) {
                $table->boolean('permiso_ver')->default(false);
                
                // Migrar datos de vuelta
                DB::statement("UPDATE accesos SET permiso_ver = permiso");
            }
        });
    }
}; 