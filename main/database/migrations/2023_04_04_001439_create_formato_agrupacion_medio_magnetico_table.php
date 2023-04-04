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
        Schema::create('Formato_Agrupacion_Medio_Magnetico', function (Blueprint $table) {
            $table->integer('Id_Formato_Agrupacion_Medio_Magnetico', true);
            $table->string('Codigo_Formato', 45)->nullable();
            $table->string('Nombre_Formato', 250)->nullable();
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
        Schema::dropIfExists('formato_agrupacion_medio_magnetico');
    }
};
