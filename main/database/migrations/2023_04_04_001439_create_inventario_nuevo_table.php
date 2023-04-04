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
        Schema::create('Inventario_Nuevo', function (Blueprint $table) {
            $table->bigInteger('Id_Inventario_Nuevo', true);
            $table->string('Codigo', 100)->nullable();
            $table->bigInteger('Id_Estiba')->nullable();
            $table->bigInteger('Id_Producto')->nullable()->index('Id_Producto');
            $table->string('Codigo_CUM', 100)->nullable();
            $table->string('Lote', 200)->nullable();
            $table->date('Fecha_Vencimiento')->nullable();
            $table->dateTime('Fecha_Carga')->useCurrent();
            $table->integer('Identificacion_Funcionario')->nullable();
            $table->bigInteger('Id_Bodega')->nullable()->default(0);
            $table->integer('Id_Punto_Dispensacion')->nullable()->default(0);
            $table->integer('Cantidad')->nullable();
            $table->integer('Lista_Ganancia')->nullable()->default(1);
            $table->integer('Id_Dispositivo')->nullable();
            $table->double('Costo')->nullable();
            $table->integer('Cantidad_Apartada')->nullable()->default(0);
            $table->string('Estiba', 100)->nullable();
            $table->integer('Fila')->nullable();
            $table->text('Alternativo')->nullable();
            $table->string('Actualizado', 10)->default('No');
            $table->integer('Cantidad_Seleccionada')->default(0);
            $table->integer('Cantidad_Leo')->nullable()->default(0);
            $table->string('Negativo', 100)->nullable();
            $table->integer('Cantidad_Pendientes')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario_nuevo');
    }
};
