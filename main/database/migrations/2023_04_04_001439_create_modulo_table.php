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
        Schema::create('Modulo', function (Blueprint $table) {
            $table->integer('Id_Modulo', true);
            $table->string('Nombre', 30);
            $table->string('Documento', 45)->nullable();
            $table->string('Prefijo', 45)->nullable();
            $table->enum('Estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulo');
    }
};
