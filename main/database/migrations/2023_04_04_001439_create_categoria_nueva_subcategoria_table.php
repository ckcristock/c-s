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
        Schema::create('categoria_nueva_subcategoria', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Id_Categoria_Nueva');
            $table->unsignedInteger('Id_Subcategoria');
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
        Schema::dropIfExists('categoria_nueva_subcategoria');
    }
};
