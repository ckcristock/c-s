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
        Schema::create('company_payment_configurations', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('calculate_work_disability', 50)->nullable();
            $table->string('pay_deductions', 50)->nullable();
            $table->string('recurring_payment', 50)->nullable();
            $table->string('payment_transport_subsidy', 50)->nullable();
            $table->enum('affects_transportation_subsidy', ['Si', 'No'])->nullable();
            $table->enum('pay_vacations', ['Si', 'No'])->nullable();
            $table->integer('company_id')->nullable();
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
        Schema::dropIfExists('company_payment_configurations');
    }
};
