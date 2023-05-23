<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusInBusinesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->enum('status', ['Prospección', 'Presupuesto', 'Cotización', 'Rechazado', 'Adjudicación'])->nullable()->default('Prospección');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->enum('status', ['Prospección', 'Presupuesto', 'Cotización', 'Negociación', 'Adjudicación'])->nullable()->default('Prospección');
        });
    }
}
