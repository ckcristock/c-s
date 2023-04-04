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
        Schema::create('Nota_Credito_Global', function (Blueprint $table) {
            $table->bigInteger('Id_Nota_Credito_Global');
            $table->string('Tipo_Factura', 50)->nullable();
            $table->bigInteger('Id_Factura')->nullable();
            $table->double('Valor_Total_Factura')->nullable();
            $table->integer('Id_Funcionario')->nullable();
            $table->integer('Id_Cliente')->nullable();
            $table->string('Codigo_Factura', 50)->nullable();
            $table->string('Codigo', 50)->nullable();
            $table->dateTime('Fecha')->nullable()->useCurrent();
            $table->string('Observaciones')->nullable();
            $table->string('Cude', 100)->nullable();
            $table->string('Codigo_Qr', 100)->nullable();
            $table->string('Procesada', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nota_credito_global');
    }
};
