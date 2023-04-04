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
        Schema::create('person_payroll_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->unsignedInteger('payroll_payment_id');
            $table->integer('worked_days');
            $table->integer('salary');
            $table->integer('transportation_assistance')->nullable()->default(0);
            $table->integer('retentions_deductions');
            $table->integer('net_salary');
            $table->timestamps();
            $table->integer('user_electronic_reported')->nullable();
            $table->timestamp('electronic_reported_date')->nullable();
            $table->tinyInteger('electronic_reported')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('cune', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_payroll_payments');
    }
};
