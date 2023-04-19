<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInProductoOrdenCompraNacional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Producto_Orden_Compra_Nacional', function (Blueprint $table) {
            $table->dropColumn('Iva');
            $table->dropColumn('Costo');
            $table->unsignedBigInteger('impuesto_id')->nullable()->after('Id_Producto');
            $table->double('Total', 50,2)->nullable()->change();
            $table->double('Subtotal', 50,2)->nullable()->after('Id_Producto');
            $table->double('Valor_Iva', 50,2)->nullable()->after('Id_Producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Producto_Orden_Compra_Nacional', function (Blueprint $table) {
            $table->integer('Iva')->nullable();
            $table->double('Costo', 30, 2)->nullable();
            $table->dropColumn('impuesto_id');
            $table->dropColumn('Subtotal');
            $table->dropColumn('Valor_Iva');
        });
    }
}
