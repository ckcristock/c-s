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
        Schema::create('Actividad_Remision', function (Blueprint $table) {
            $table->bigIncrements('Id_Actividad_Remision');
            $table->bigInteger('Id_Remision')->nullable();
            $table->bigInteger('Identificacion_Funcionario')->nullable();
            $table->timestamp('Fecha')->useCurrentOnUpdate()->useCurrent();
            $table->text('Detalles')->nullable();
            $table->string('Estado', 100)->default('Creacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_remision');
    }
};
