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
        Schema::create('Medio_Magnetico_Agrupacion', function (Blueprint $table) {
            $table->integer('Id_Medio_Magnetico_Agrupacion', true);
            $table->integer('Id_Formato_Agrupacion_Medio_Magnetico')->nullable()->index('Id_Formato_Agrupacion_Medio_Magnetico_idx');
            $table->integer('Id_Medio_Magnetico_Especial')->nullable()->index('Id_Medio_Magnetico_Especial_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medio_magnetico_agrupacion');
    }
};
