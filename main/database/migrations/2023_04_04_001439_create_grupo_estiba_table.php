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
        Schema::create('Grupo_Estiba', function (Blueprint $table) {
            $table->bigInteger('Id_Grupo_Estiba', true);
            $table->integer('Id_Punto_Dispensacion')->nullable();
            $table->string('Nombre', 60);
            $table->integer('Id_Bodega_Nuevo')->nullable()->index('FK_Id_Bodega_Nuevo');
            $table->string('Fecha_Vencimiento', 10)->nullable();
            $table->string('Presentacion', 10)->nullable();
            $table->enum('Estado', ['Activo', 'Inactivo'])->default('Activo');
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
        Schema::dropIfExists('grupo_estiba');
    }
};
