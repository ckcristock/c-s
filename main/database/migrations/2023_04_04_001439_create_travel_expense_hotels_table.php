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
        Schema::create('travel_expense_hotels', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('travel_expense_id');
            $table->bigInteger('hotel_id');
            $table->integer('n_night');
            $table->string('who_cancels', 50);
            $table->bigInteger('accommodation')->default(0);
            $table->enum('breakfast', ['Si', 'No']);
            $table->double('total', 50, 2)->nullable();
            $table->double('rate', 50, 2)->nullable();
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
        Schema::dropIfExists('travel_expense_hotels');
    }
};
