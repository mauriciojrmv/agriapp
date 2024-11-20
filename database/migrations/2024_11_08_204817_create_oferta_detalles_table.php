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
        Schema::create('oferta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produccion')->constrained('produccions');
            $table->foreignId('id_oferta')->constrained('ofertas');
            $table->foreignId('id_unidadmedida')->constrained('unidad_medidas');
            $table->foreignId('id_moneda')->constrained('monedas');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_fisico');
            $table->integer('cantidad_comprometido')->default(0);
            $table->decimal('precio', 10, 2);
            $table->decimal('preciounitario', 10, 2)->nullable();
            $table->string('estado')->default('activo');
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
        Schema::dropIfExists('oferta_detalles');
    }
};
