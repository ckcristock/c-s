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
        Schema::create('Subcategoria', function (Blueprint $table) {
            $table->integer('Id_Subcategoria', true);
            $table->integer('Id_Categoria_Nueva')->nullable();
            $table->string('Nombre', 100);
            $table->enum('Separable', ['Si', 'No'])->nullable()->default('No');
            $table->boolean('Activo')->default(false);
            $table->boolean('Fijo');
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
        Schema::dropIfExists('subcategoria');
    }
};
