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
        Schema::create('Actividad_Contabilidad', function (Blueprint $table) {
            $table->bigIncrements('Id_Actividad_Contabilidad');
            $table->bigInteger('Id_Registro')->nullable();
            $table->bigInteger('Identificacion_Funcionario')->nullable();
            $table->timestamp('Fecha')->useCurrent();
            $table->text('Detalles')->nullable();
            $table->string('Estado', 100)->default('Creacion');
            $table->string('Modulo', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_contabilidad');
    }
};
