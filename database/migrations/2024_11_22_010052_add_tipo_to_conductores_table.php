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
    Schema::table('conductors', function (Blueprint $table) {
        $table->string('tipo')->default('recogo')->after('estado'); // Por defecto, tipo será 'recogo'
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('conductors', function (Blueprint $table) {
        $table->dropColumn('tipo');
    });
    }
};
