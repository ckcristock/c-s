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
        Schema::create('rrhh_activities', function (Blueprint $table) {
            $table->integer('id', true)->index('Índice 1');
            $table->string('description', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('user_id', 100)->nullable();
            $table->timestamps();
            $table->timestamp('date_end')->nullable();
            $table->timestamp('date_start')->nullable();
            $table->time('hour_start')->nullable();
            $table->time('hour_end')->nullable();
            $table->enum('state', ['Anulada', 'Aprobada'])->nullable()->default('Aprobada');
            $table->integer('rrhh_activity_type_id')->nullable();
            $table->integer('dependency_id')->nullable();
            $table->string('code', 150);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_activities');
    }
};
