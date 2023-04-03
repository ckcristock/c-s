<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApuServiceTravelEstimationDimensionalValidationContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apu_service_travel_estimation_dimensional_validation_contractors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apu_service_dimensional_validation_contractors_id')->nullable();
            $table->string('description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('amount', 50, 7)->nullable();
            $table->decimal('unit_value', 50, 7)->nullable();
            $table->string('formula_amount')->nullable();
            $table->string('formula_total_value')->nullable();
            $table->unsignedBigInteger('travel_expense_estimation_id')->nullable();
            $table->decimal('total_value', 50, 7)->nullable();
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
        Schema::dropIfExists('apu_service_travel_estimation_dimensional_validation_contractors');
    }
}
