<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSetAndMachineInApuParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apu_parts', function (Blueprint $table) {
            $table->string('set_name', 255)->nullable();
            $table->string('machine_name', 255)->nullable();
        });
        Schema::table('apu_services', function (Blueprint $table) {
            $table->string('set_name', 255)->nullable();
            $table->string('machine_name', 255)->nullable();
        });
        Schema::table('apu_sets', function (Blueprint $table) {
            $table->string('set_name', 255)->nullable();
            $table->string('machine_name', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apu_parts', function (Blueprint $table) {
            $table->dropColumn('set_name');
            $table->dropColumn('machine_name');
        });
        Schema::table('apu_services', function (Blueprint $table) {
            $table->dropColumn('set_name');
            $table->dropColumn('machine_name');
        });
        Schema::table('apu_sets', function (Blueprint $table) {
            $table->dropColumn('set_name');
            $table->dropColumn('machine_name');
        });
    }
}
