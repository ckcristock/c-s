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
        Schema::create('producto_pre_compra', function (Blueprint $table) {
            $table->integer('Id_Producto_Pre_Compra', true);
            $table->integer('Id_Pre_Compra')->nullable();
            $table->integer('Id_Producto')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->float('Costo', 10, 0)->nullable();
            $table->timestamp('Fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_pre_compra');
    }
};
