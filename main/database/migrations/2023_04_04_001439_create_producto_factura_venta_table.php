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
        Schema::create('Producto_Factura_Venta', function (Blueprint $table) {
            $table->integer('Id_Producto_Factura_Venta', true);
            $table->bigInteger('Id_Factura_Venta')->nullable();
            $table->bigInteger('Id_Inventario_Nuevo')->nullable();
            $table->bigInteger('Id_Producto')->nullable();
            $table->string('Lote', 200)->nullable();
            $table->date('Fecha_Vencimiento')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->decimal('Precio_Venta', 20)->nullable();
            $table->integer('Impuesto')->nullable();
            $table->decimal('Subtotal', 20)->nullable();
            $table->bigInteger('Id_Remision')->nullable();
            $table->string('Invima', 100)->nullable();
            $table->longText('producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_factura_venta');
    }
};
