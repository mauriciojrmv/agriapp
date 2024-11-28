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
        Schema::create('ruta_carga_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga_pedido')->constrained('carga_pedidos');
            $table->foreignId('id_ruta_pedido')->constrained('ruta_pedidos');
            $table->foreignId('id_transporte')->constrained('transportes');
            $table->integer('orden');
            $table->integer('cantidad');
            $table->string('estado')->default('pendiente');
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
        Schema::dropIfExists('ruta_carga_pedidos');
    }
};
