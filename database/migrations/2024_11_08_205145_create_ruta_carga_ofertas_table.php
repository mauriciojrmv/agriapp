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
        Schema::create('ruta_carga_ofertas', function (Blueprint $table) {
            $table->foreignId('id_carga_oferta')->constrained('carga_ofertas');
            $table->foreignId('id_ruta_oferta')->constrained('ruta_ofertas');
            $table->foreignId('id_transporte')->constrained('transportes');
            $table->integer('orden');
            $table->string('estado');
            $table->decimal('distancia', 10, 2);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ruta_carga_ofertas');
    }
};
