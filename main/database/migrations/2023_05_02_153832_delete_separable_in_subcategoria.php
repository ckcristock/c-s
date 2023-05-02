<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteSeparableInSubcategoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Subcategoria', function (Blueprint $table) {
            $table->dropColumn('Separable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Subcategoria', function (Blueprint $table) {
            $table->enum('Separable', ['Si', 'No'])->nullable()->default('No')->after('Nombre');
        });
    }
}
