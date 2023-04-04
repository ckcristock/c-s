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
        Schema::create('late_arrivals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->integer('time')->nullable();
            $table->time('entry');
            $table->time('real_entry');
            $table->boolean('count')->default(true);
            $table->string('justification', 191)->nullable();
            $table->timestamps();
            $table->timestamp('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('late_arrivals');
    }
};
