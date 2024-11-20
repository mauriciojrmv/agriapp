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
        Schema::create('conductors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('carnet');
            $table->string('licencia_conducir');
            $table->date('fecha_nacimiento');
            $table->string('direccion');
            $table->string('email')->unique();
            $table->string('password');
            $table->decimal('ubicacion_latitud', 10, 7)->nullable();
            $table->decimal('ubicacion_longitud', 10, 7)->nullable();
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
        Schema::dropIfExists('conductores');
    }
};
