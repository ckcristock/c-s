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
        Schema::create('companies', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 150)->nullable();
            $table->string('work_time', 50)->nullable();
            $table->string('logo', 150)->nullable();
            $table->string('social_reason', 150)->nullable();
            $table->string('document_type', 150)->nullable();
            $table->integer('document_number')->nullable();
            $table->date('constitution_date')->nullable();
            $table->string('email_contact', 50)->nullable();
            $table->integer('phone')->nullable();
            $table->integer('verification_digit')->nullable();
            $table->integer('max_extras_hours')->nullable();
            $table->integer('max_holidays_legal')->nullable();
            $table->integer('max_late_arrival')->nullable();
            $table->string('base_salary', 191)->nullable();
            $table->string('paid_operator', 191)->nullable();
            $table->integer('transportation_assistance')->nullable();
            $table->time('night_start_time')->nullable();
            $table->time('night_end_time')->nullable();
            $table->string('holidays', 50)->nullable();
            $table->string('payment_frequency', 50)->nullable();
            $table->integer('account_number')->nullable();
            $table->string('account_type', 50)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->enum('law_1429', ['Si', 'No'])->nullable();
            $table->enum('law_590', ['Si', 'No'])->nullable();
            $table->enum('law_1607', ['Si', 'No'])->nullable();
            $table->integer('arl_id')->nullable();
            $table->integer('bank_id')->nullable();
            $table->timestamps();
            $table->string('address')->nullable();
            $table->string('page_heading')->nullable();
            $table->text('commercial_terms', 10000)->nullable();
            $table->text('technical_requirements', 5000)->nullable();
            $table->text('legal_requirements', 5000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
