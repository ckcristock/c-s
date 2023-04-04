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
        Schema::create('travel_expense_estimations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('description', 191)->nullable();
            $table->string('unit', 191)->nullable();
            $table->string('displacement', 191)->default('[]');
            $table->string('destination', 191)->default('[]');
            $table->double('land_national_value', 50, 7)->nullable();
            $table->double('land_international_value', 50, 7)->nullable();
            $table->double('aerial_national_value', 50, 7)->nullable();
            $table->double('aerial_international_value', 50, 7)->nullable();
            $table->double('international_value', 50, 7)->nullable();
            $table->double('national_value', 50, 7)->nullable();
            $table->double('unit_value', 50, 7)->nullable();
            $table->string('formula_amount', 500)->nullable();
            $table->string('formula_total_value', 500)->nullable();
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
        Schema::dropIfExists('travel_expense_estimations');
    }
};
