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
        Schema::create('task_timelines', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('icon', 50)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->integer('task_id')->nullable();
            $table->integer('person_id')->nullable();
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
        Schema::dropIfExists('task_timelines');
    }
};
