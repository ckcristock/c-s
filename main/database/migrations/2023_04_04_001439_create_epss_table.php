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
        Schema::create('epss', function (Blueprint $table) {
            $table->integer('id', true)->index('Índice 1');
            $table->string('name', 150)->nullable();
            $table->bigInteger('nit')->nullable()->unique('nit');
            $table->string('code', 50)->nullable()->unique('code');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('epss');
    }
};
