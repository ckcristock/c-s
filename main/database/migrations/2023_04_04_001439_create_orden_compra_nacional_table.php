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
        Schema::create('Orden_Compra_Nacional', function (Blueprint $table) {
            $table->bigInteger('Id_Orden_Compra_Nacional', true);
            $table->string('Codigo', 10)->nullable();
            $table->integer('Identificacion_Funcionario')->nullable();
            $table->timestamp('Fecha')->nullable()->useCurrent();
            $table->bigInteger('Id_Bodega')->nullable()->default(0);
            $table->enum('Tipo_Bodega', ['Bodega', 'Punto'])->nullable()->default('Bodega');
            $table->bigInteger('Id_Bodega_Nuevo')->nullable();
            $table->integer('Id_Punto_Dispensacion')->nullable()->default(0);
            $table->bigInteger('Id_Proveedor')->nullable();
            $table->text('Observaciones')->nullable();
            $table->date('Fecha_Entrega_Probable')->nullable();
            $table->string('Tipo', 100)->nullable();
            $table->enum('Estado', ['Pendiente', 'Anulada', 'Aprobada', 'Rechazada'])->nullable()->default('Pendiente');
            $table->timestamp('Fecha_Creacion_Compra')->useCurrent();
            $table->string('Codigo_Qr', 100)->default('');
            $table->enum('Aprobacion', ['Aprobada', 'Rechazada', 'Pendiente'])->default('Pendiente');
            $table->bigInteger('Id_Pre_Compra')->nullable();
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
        Schema::dropIfExists('orden_compra_nacional');
    }
};
