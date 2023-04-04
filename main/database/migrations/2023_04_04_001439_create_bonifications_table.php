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
        Schema::create('bonifications', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('countable_income_id')->nullable();
            $table->double('value', 50, 2)->nullable();
            $table->integer('work_contract_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonifications');
    }
};
