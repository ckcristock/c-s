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
        Schema::create('rrhh_activity_people', function (Blueprint $table) {
            $table->integer('id', true)->index('Índice 1');
            $table->integer('person_id')->nullable();
            $table->integer('rrhh_activity_id')->nullable();
            $table->enum('state', ['Activo', 'Cancelado'])->nullable()->default('Activo');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_activity_people');
    }
};
