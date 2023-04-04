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
        Schema::create('Costo_Promedio', function (Blueprint $table) {
            $table->bigInteger('Id_Costo_Promedio', true);
            $table->bigInteger('Id_Producto')->nullable();
            $table->decimal('Costo_Promedio', 50)->nullable();
            $table->dateTime('Ultima_Actualizacion')->nullable();
            $table->decimal('Costo_Anterior', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costo_promedio');
    }
};
