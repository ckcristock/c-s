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
        Schema::create('Cuenta_Contable_Comprobante', function (Blueprint $table) {
            $table->integer('Id_Cuenta_Contable_Comprobante', true);
            $table->integer('Id_Plan_Cuenta')->index('Id_Plan_Cuenta');
            $table->float('Valor', 10, 0);
            $table->integer('Impuesto');
            $table->integer('Cantidad');
            $table->text('Observaciones');
            $table->float('Subtotal', 10, 0);
            $table->integer('Id_Comprobante')->index('Id_Comprobante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuenta_contable_comprobante');
    }
};
