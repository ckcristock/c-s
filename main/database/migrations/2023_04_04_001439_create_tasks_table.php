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
        Schema::create('tasks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_realizador');
            $table->integer('type_id');
            $table->string('titulo', 50);
            $table->string('descripcion', 250)->nullable();
            $table->date('fecha');
            $table->binary('adjunto')->nullable();
            $table->string('link', 50)->nullable();
            $table->integer('id_asignador');
            $table->time('hora')->nullable();
            $table->enum('estado', ['Pendiente', 'En ejecucion', 'Archivada', 'En espera', 'Finalizado'])->default('Pendiente');
            $table->string('category', 100)->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
