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
        Schema::create('apu_service_travel_estimation_assembly_start_ups', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('apu_service_assembly_start_up_id')->nullable();
            $table->string('description', 191)->nullable();
            $table->string('unit', 191)->nullable();
            $table->double('amount', 50, 7)->nullable();
            $table->double('unit_value', 50, 7)->nullable();
            $table->string('formula_amount', 500)->nullable();
            $table->string('formula_total_value', 500)->nullable();
            $table->integer('travel_expense_estimation_id')->nullable();
            $table->double('total_value', 50, 7)->nullable();
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
        Schema::dropIfExists('apu_service_travel_estimation_assembly_start_ups');
    }
};
