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
        Schema::create('premium_people', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('premium_id')->nullable();
            $table->integer('person_id')->nullable();
            $table->integer('digit_person')->nullable();
            $table->string('details', 500)->nullable();
            $table->integer('worked_days')->nullable();
            $table->integer('salary')->nullable();
            $table->integer('total_premium')->nullable();
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
        Schema::dropIfExists('premium_people');
    }
};
