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
        Schema::create('Cronograma_Remision', function (Blueprint $table) {
            $table->integer('Id_Cronograma_Remision', true);
            $table->string('Dia', 20);
            $table->integer('Semana');
            $table->date('Fecha_Asignacion');
            $table->integer('Id_Funcionario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cronograma_remision');
    }
};
