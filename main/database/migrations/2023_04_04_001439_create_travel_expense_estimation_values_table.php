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
        Schema::create('travel_expense_estimation_values', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('travel_expense_estimation_id')->nullable();
            $table->double('land_national_value', 50, 2)->nullable();
            $table->double('land_international_value', 50, 2)->nullable();
            $table->double('aerial_national_value', 50, 2)->nullable();
            $table->double('aerial_international_value', 50, 2)->nullable();
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
        Schema::dropIfExists('travel_expense_estimation_values');
    }
};
