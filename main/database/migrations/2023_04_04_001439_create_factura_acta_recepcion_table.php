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
        Schema::create('Factura_Acta_Recepcion', function (Blueprint $table) {
            $table->bigInteger('Id_Factura_Acta_Recepcion', true);
            $table->bigInteger('Id_Acta_Recepcion')->nullable();
            $table->string('Factura', 100)->nullable();
            $table->date('Fecha_Factura');
            $table->string('Archivo_Factura', 200)->nullable();
            $table->bigInteger('Id_Orden_Compra')->nullable();
            $table->string('Tipo_Compra', 100);
            $table->string('Estado', 60)->default('Pendiente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factura_acta_recepcion');
    }
};
