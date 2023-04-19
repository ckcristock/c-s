<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Producto', function (Blueprint $table) {
            $table->dropColumn('Gravado');
            $table->unsignedBigInteger('impuesto_id')->nullable()->after('Referencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Producto', function (Blueprint $table) {
            $table->dropColumn('impuesto_id');
            $table->enum('Gravado', ['Si', 'No'])->default('No')->after('Referencia');
        });
    }
}
