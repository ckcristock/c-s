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
        Schema::create('rrhh_activity_types', function (Blueprint $table) {
            $table->integer('id', true)->index('Índice 1');
            $table->string('name', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->timestamps();
            $table->enum('state', ['Activo', 'Cancelado'])->nullable()->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_activity_types');
    }
};
