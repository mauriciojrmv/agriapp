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
        Schema::create('carga_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido_detalle')->constrained('pedido_detalles');
            $table->integer('cantidad');
            $table->integer('cantidad_i')->default(0);
            $table->string('estado');
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
        Schema::dropIfExists('carga_pedidos');
    }
};
