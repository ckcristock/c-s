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
        Schema::create('Lista_Ganancia', function (Blueprint $table) {
            $table->bigInteger('Id_Lista_Ganancia', true);
            $table->string('Codigo', 100)->nullable();
            $table->string('Nombre', 100)->nullable();
            $table->integer('Porcentaje')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_ganancia');
    }
};
