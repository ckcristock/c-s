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
        Schema::create('Inventario_Contrato', function (Blueprint $table) {
            $table->increments('Id_Inventario_Contrato');
            $table->integer('Id_Contrato')->nullable();
            $table->integer('Id_Inventario_Nuevo')->nullable();
            $table->integer('Id_Producto_Contrato')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->integer('Cantidad_Apartada')->nullable();
            $table->integer('Cantidad_Seleccionada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario_contrato');
    }
};
