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
        Schema::create('pay_configuration_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('disability_percentage_id', 191);
            $table->string('deductions_pay', 191);
            $table->string('recurrent_pay', 191);
            $table->string('pay_transportation_assistance', 191);
            $table->boolean('affect_transportation_assistance');
            $table->boolean('vacations_31_pay')->default(false);
            $table->unsignedInteger('company_id');
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
        Schema::dropIfExists('pay_configuration_companies');
    }
};
