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
        Schema::create('Contabilidad_Comprobante', function (Blueprint $table) {
            $table->integer('Id_Contabilidad_Comprobante', true);
            $table->integer('Id_Plan_Cuentas')->nullable()->index('Id_Plan_Cuentas_idx');
            $table->integer('Id_Comprobante')->nullable()->index('Id_Comprobante_idx');
            $table->string('Id_Factura_Comprobante', 45)->nullable()->index('Id_Factura_Comprobante_idx');
            $table->float('Debito', 10, 0)->nullable()->default(0);
            $table->float('Credito', 10, 0)->nullable()->default(0);
            $table->string('Codigo_Cuenta', 45)->nullable();
            $table->text('Nombre_Cuenta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contabilidad_comprobante');
    }
};
