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
        Schema::create('pension_funds', function (Blueprint $table) {
            $table->integer('id', true)->index('Índice 1');
            $table->string('name', 150)->nullable();
            $table->integer('code');
            $table->integer('nit');
            $table->string('updated_at', 50);
            $table->string('created_at', 50);
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pension_funds');
    }
};
