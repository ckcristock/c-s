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
        Schema::create('Nota_Credito', function (Blueprint $table) {
            $table->bigInteger('Id_Nota_Credito', true);
            $table->text('Observacion')->nullable();
            $table->bigInteger('Id_Factura')->nullable();
            $table->string('Codigo', 100)->nullable();
            $table->timestamp('Fecha')->useCurrent();
            $table->string('Codigo_Qr', 100)->default('');
            $table->integer('Identificacion_Funcionario')->nullable();
            $table->integer('Id_Cliente')->nullable();
            $table->string('Estado', 100)->nullable()->default('Pendiente');
            $table->text('Motivo_Rechazo')->nullable();
            $table->dateTime('Fecha_Anulacion')->nullable();
            $table->integer('Funcionario_Anula')->nullable();
            $table->bigInteger('Id_Bodega_Nuevo')->nullable();
            $table->string('Cude', 200)->nullable();
            $table->string('Procesada', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nota_credito');
    }
};
