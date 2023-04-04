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
        Schema::create('Medio_Magnetico', function (Blueprint $table) {
            $table->integer('Id_Medio_Magnetico', true);
            $table->integer('Periodo')->nullable();
            $table->string('Codigo_Formato', 60)->nullable();
            $table->string('Nombre_Formato', 250)->nullable();
            $table->string('Tipo_Exportacion', 60)->nullable();
            $table->text('Detalles')->nullable();
            $table->text('Tipos')->nullable();
            $table->string('Tipo_Medio_Magnetico', 45)->nullable()->default('Basico');
            $table->string('Tipo_Columna', 45)->nullable();
            $table->string('Columna_Principal', 250)->nullable();
            $table->string('Estado', 45)->nullable()->default('Activo');
            $table->integer('Id_Empresa')->nullable();
            $table->timestamp('Created_At')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medio_magnetico');
    }
};
