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
        Schema::create('Producto_Nota_Credito', function (Blueprint $table) {
            $table->integer('Id_Producto_Nota_Credito', true);
            $table->bigInteger('Id_Nota_Credito')->nullable();
            $table->bigInteger('Id_Inventario')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->decimal('Precio_Venta', 20)->nullable();
            $table->decimal('Subtotal', 20)->nullable();
            $table->integer('Impuesto')->nullable();
            $table->integer('Id_Producto')->nullable();
            $table->integer('Id_Motivo')->nullable();
            $table->string('Lote', 100)->nullable();
            $table->date('Fecha_Vencimiento')->nullable();
            $table->text('Observacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_nota_credito');
    }
};
