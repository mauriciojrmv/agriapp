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
        Schema::table('agricultors', function (Blueprint $table) {
            $table->string('tokendevice')->nullable()->unique(); // Hacer tokendevice único
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agricultors', function (Blueprint $table) {
            $table->dropColumn('tokendevice');
        });
    }
};
