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
        Schema::create('businesses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 200);
            $table->string('format_code', 200);
            $table->string('name', 250);
            $table->integer('third_party_id');
            $table->integer('third_party_person_id');
            $table->integer('country_id');
            $table->string('description', 500)->nullable();
            $table->enum('status', ['Prospección', 'Presupuesto', 'Cotización', 'Negociación', 'Adjudicación'])->nullable()->default('Prospección');
            $table->integer('city_id');
            $table->date('date');
            $table->double('budget_value', 50, 2);
            $table->double('quotation_value', 50, 2);
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
        Schema::dropIfExists('businesses');
    }
};
