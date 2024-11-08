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
        Schema::create('produccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_terreno')->constrained('terrenos');
            $table->foreignId('id_temporada')->constrained('temporadas');
            $table->foreignId('id_producto')->constrained('productos');
            $table->foreignId('id_unidadmedida')->constrained('unidad_medidas');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad');
            $table->date('fecha_cosecha');
            $table->date('fecha_expiracion')->nullable();
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
        Schema::dropIfExists('produccions');
    }
};
