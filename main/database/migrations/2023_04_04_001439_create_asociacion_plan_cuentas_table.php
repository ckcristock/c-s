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
        Schema::create('Asociacion_Plan_Cuentas', function (Blueprint $table) {
            $table->integer('Id_Asociacion_Plan_Cuentas', true);
            $table->integer('Id_Plan_Cuenta');
            $table->integer('Id_Modulo');
            $table->string('Busqueda_Interna', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asociacion_plan_cuentas');
    }
};
