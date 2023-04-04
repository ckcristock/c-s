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
        Schema::create('Categoria_Nueva', function (Blueprint $table) {
            $table->bigInteger('Id_Categoria_Nueva', true);
            $table->string('Nombre', 200)->nullable();
            $table->enum('Compra_Internacional', ['Si', 'No'])->default('No');
            $table->enum('Aplica_Separacion_Categorias', ['Si', 'No'])->nullable()->default('No');
            $table->tinyInteger('Activo')->default(1);
            $table->tinyInteger('Fijo')->default(0);
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
        Schema::dropIfExists('categoria_nueva');
    }
};
