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
        Schema::create('Producto_Orden_Compra_Nacional', function (Blueprint $table) {
            $table->bigInteger('Id_Producto_Orden_Compra_Nacional', true);
            $table->bigInteger('Id_Orden_Compra_Nacional')->nullable();
            $table->bigInteger('Id_Inventario')->nullable();
            $table->bigInteger('Id_Producto')->nullable();
            $table->decimal('Costo', 30)->nullable();
            $table->integer('Cantidad')->nullable();
            $table->integer('Iva')->nullable();
            $table->decimal('Total', 30)->nullable();
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
        Schema::dropIfExists('producto_orden_compra_nacional');
    }
};
