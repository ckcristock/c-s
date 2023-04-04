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
        Schema::create('Inventario_Fisico_Nuevo', function (Blueprint $table) {
            $table->integer('Id_Inventario_Fisico_Nuevo', true);
            $table->integer('Funcionario_Autoriza');
            $table->integer('Id_Bodega_Nuevo');
            $table->integer('Id_Grupo_Estiba');
            $table->date('Fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario_fisico_nuevo');
    }
};
