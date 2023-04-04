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
        Schema::create('Orden_Pedido', function (Blueprint $table) {
            $table->integer('Id_Orden_Pedido', true);
            $table->integer('Id_Agentes_Cliente')->default(0);
            $table->string('Orden_Compra_Cliente')->default('0');
            $table->string('Archivo_Compra_Cliente')->default('0');
            $table->date('Fecha_Probable_Entrega')->nullable();
            $table->integer('Identificacion_Funcionario')->nullable()->default(0);
            $table->string('Observaciones', 2000)->nullable();
            $table->timestamp('Fecha')->nullable()->useCurrent();
            $table->integer('Id_Cliente')->nullable();
            $table->string('Estado', 50)->nullable();

            $table->index(['Id_Orden_Pedido'], 'Índice 1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_pedido');
    }
};
