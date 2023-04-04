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
        Schema::create('apu_service_dimensional_validations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('apu_service_id')->nullable();
            $table->string('apu_profile_id', 191)->nullable();
            $table->string('displacement_type', 191)->nullable();
            $table->string('observation', 500)->nullable();
            $table->integer('days_number_displacement')->nullable();
            $table->integer('days_number_festive')->nullable();
            $table->integer('days_number_ordinary')->nullable();
            $table->double('hours_value_displacement', 50, 7)->nullable();
            $table->double('hours_value_festive', 50, 7)->nullable();
            $table->double('hours_value_ordinary', 50, 7)->nullable();
            $table->integer('hours_displacement')->nullable();
            $table->integer('hours_festive')->nullable();
            $table->integer('hours_ordinary')->nullable();
            $table->integer('people_number')->nullable();
            $table->double('salary_value', 50, 7)->nullable();
            $table->double('subtotal', 50, 7)->nullable();
            $table->double('total_value_displacement', 50, 7)->nullable();
            $table->double('total_value_festive', 50, 7)->nullable();
            $table->double('total_value_ordinary', 50, 7)->nullable();
            $table->string('workind_day_displacement', 191)->nullable();
            $table->string('working_day_festive', 191)->nullable();
            $table->string('working_day_ordinary', 191)->nullable();
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
        Schema::dropIfExists('apu_service_dimensional_validations');
    }
};
