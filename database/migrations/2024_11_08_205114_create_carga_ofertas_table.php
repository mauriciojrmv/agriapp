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
        Schema::create('carga_ofertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_oferta_detalle')->constrained('oferta_detalles');
            $table->integer('pesokg');
            $table->decimal('precio', 10, 2);
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
        Schema::dropIfExists('carga_ofertas');
    }
};
