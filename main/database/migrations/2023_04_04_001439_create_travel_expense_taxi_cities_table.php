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
        Schema::create('travel_expense_taxi_cities', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('journeys');
            $table->bigInteger('taxi_city_id');
            $table->string('rate', 50)->default('');
            $table->double('total', 50, 2)->nullable();
            $table->bigInteger('travel_expense_id');
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
        Schema::dropIfExists('travel_expense_taxi_cities');
    }
};
