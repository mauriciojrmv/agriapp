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
        Schema::create('terrenos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_agricultor')->constrained('agricultors');
            $table->text('descripcion')->nullable();
            $table->decimal('area', 10, 2);
            $table->decimal('superficie_total', 10, 2);
            $table->decimal('ubicacion_latitud', 10, 7);
            $table->decimal('ubicacion_longitud', 10, 7);
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
        Schema::dropIfExists('terrenos');
    }
};
