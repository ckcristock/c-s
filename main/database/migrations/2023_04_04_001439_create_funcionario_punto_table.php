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
        Schema::create('Funcionario_Punto', function (Blueprint $table) {
            $table->bigInteger('Id_Funcionario_Punto', true);
            $table->bigInteger('Id_Punto_Dispensacion')->nullable();
            $table->bigInteger('Identificacion_Funcionario')->nullable();
            $table->timestamp('Fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funcionario_punto');
    }
};
