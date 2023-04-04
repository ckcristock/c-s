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
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('code', 50)->nullable();
            $table->string('payment_frequency', 50)->nullable();
            $table->date('start_period');
            $table->date('end_period');
            $table->integer('total_salaries');
            $table->integer('total_retentions');
            $table->integer('total_provisions');
            $table->integer('total_social_secturity');
            $table->integer('total_parafiscals');
            $table->integer('total_overtimes_surcharges');
            $table->integer('total_incomes');
            $table->integer('total_cost');
            $table->timestamps();
            $table->tinyInteger('electronic_reported')->nullable();
            $table->timestamp('electronic_reported_date')->nullable();
            $table->integer('user_electronic_reported')->nullable();
            $table->integer('company_id')->nullable();
            $table->tinyInteger('email_reported')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_payments');
    }
};
