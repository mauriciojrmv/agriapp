<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ruta_carga_ofertas', function (Blueprint $table) {
            $table->string('estado_conductor')->default('pendiente')->after('id_transporte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ruta_carga_ofertas', function (Blueprint $table) {
            $table->dropColumn('estado_conductor');
        });
    }
};

