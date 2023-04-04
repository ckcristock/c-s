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
        Schema::create('Pre_Compra', function (Blueprint $table) {
            $table->bigInteger('Id_Pre_Compra', true);
            $table->integer('Identificacion_Funcionario')->nullable();
            $table->dateTime('Fecha')->nullable()->useCurrent();
            $table->integer('Id_Orden_Pedido')->nullable();
            $table->integer('Id_Proveedor')->nullable();
            $table->string('Estado', 100)->default('Pendiente');
            $table->enum('Tipo', ['Medicamentos', 'Materiales', 'Nutriciones', 'Orden_Pedido'])->nullable();
            $table->enum('Tipo_Medicamento', ['Todos', 'Clientes', 'No Pos', 'Pos'])->nullable();
            $table->date('Fecha_Inicio')->nullable();
            $table->date('Fecha_Fin')->nullable();
            $table->enum('Excluir_Vencimiento', ['Si', 'No'])->nullable();
            $table->integer('Meses')->nullable();
            $table->bigInteger('Id_Orden_Compra_Nacional')->nullable();
            $table->integer('Id_Contrato')->nullable();
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
        Schema::dropIfExists('pre_compra');
    }
};
