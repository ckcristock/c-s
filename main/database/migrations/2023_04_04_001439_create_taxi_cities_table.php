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
        Schema::create('taxi_cities', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->enum('type', ['Nacional', 'Internacional'])->nullable();
            $table->bigInteger('taxi_id');
            $table->bigInteger('city_id');
            $table->double('value', 50, 2);
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
        Schema::dropIfExists('taxi_cities');
    }
};
