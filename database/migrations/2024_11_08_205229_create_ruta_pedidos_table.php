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
        Schema::create('ruta_pedidos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_entrega');
            $table->decimal('capacidad_utilizada', 10, 2);
            $table->decimal('distancia_total', 10, 2);
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
        Schema::dropIfExists('ruta_pedidos');
    }
};
