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
        Schema::create('loan_fees', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('loan_id')->nullable();
            $table->integer('number')->nullable();
            $table->decimal('amortization', 50)->nullable();
            $table->decimal('interest', 50)->nullable();
            $table->decimal('value', 50)->nullable();
            $table->decimal('outstanding_balance', 50)->nullable();
            $table->date('date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->enum('state', ['Pendiente', 'Paga'])->nullable()->default('Pendiente');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_fees');
    }
};
