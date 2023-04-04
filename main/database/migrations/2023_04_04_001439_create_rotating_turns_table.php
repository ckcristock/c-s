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
        Schema::create('rotating_turns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->boolean('extra_hours')->nullable()->default(false);
            $table->integer('entry_tolerance')->default(0);
            $table->integer('leave_tolerance')->default(0);
            $table->enum('state', ['Activo', 'Inactivo'])->default('Activo');
            $table->tinyInteger('launch')->nullable()->default(0);
            $table->tinyInteger('breack')->nullable()->default(0);
            $table->time('entry_time')->default('00:00:00');
            $table->time('leave_time')->default('00:00:00');
            $table->time('launch_time')->default('00:00:00');
            $table->time('launch_time_two')->default('00:00:00');
            $table->time('breack_time')->default('00:00:00');
            $table->time('breack_time_two')->default('00:00:00');
            $table->integer('sunday_id')->nullable();
            $table->integer('saturday_id')->nullable();
            $table->string('color', 191);
            $table->timestamps();
            $table->unsignedInteger('empresa_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotating_turns');
    }
};
