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
        Schema::create('Departamento', function (Blueprint $table) {
            $table->integer('Id_Departamento', true);
            $table->string('Nombre', 200)->nullable();
            $table->string('Codigo', 2)->nullable();

            $table->index(['Id_Departamento'], 'Id_Departamento_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departamento');
    }
};
