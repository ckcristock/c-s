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
        Schema::create('business_histories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('business_id');
            $table->string('icon', 50);
            $table->string('title', 50);
            $table->integer('person_id')->default(0);
            $table->string('description', 250);
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
        Schema::dropIfExists('business_histories');
    }
};
