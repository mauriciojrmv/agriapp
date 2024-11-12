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
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido')->constrained('pedidos');
            $table->foreignId('id_producto')->constrained('productos');
            $table->foreignId('id_unidadmedida')->constrained('unidad_medidas');
            $table->integer('cantidad');
            $table->integer('cantidad_ofertada');
            $table->string('estado_ofertado')->default('pendiente');
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
        Schema::dropIfExists('pedido_detalles');
    }
};
