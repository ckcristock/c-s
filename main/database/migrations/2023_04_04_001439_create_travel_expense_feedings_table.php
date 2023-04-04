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
        Schema::create('travel_expense_feedings', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->enum('type', ['Nacional', 'Internacional']);
            $table->enum('breakfast', ['Si', 'No']);
            $table->integer('rate')->nullable();
            $table->integer('travel_expense_id')->nullable();
            $table->integer('stay')->nullable();
            $table->double('total', 50, 2)->nullable();
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
        Schema::dropIfExists('travel_expense_feedings');
    }
};
