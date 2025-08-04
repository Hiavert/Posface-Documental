<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documento_administrativo', function (Blueprint $table) {
            $table->unsignedInteger('fk_id_tipo')->nullable()->after('fk_id_usuario');
            $table->foreign('fk_id_tipo')->references('id_tipo')->on('tipo_documento');
        });
    }

    public function down()
    {
        Schema::table('documento_administrativo', function (Blueprint $table) {
            $table->dropForeign(['fk_id_tipo']);
            $table->dropColumn('fk_id_tipo');
        });
    }
}; 