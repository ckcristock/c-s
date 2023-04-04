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
        Schema::create('pretty_cash', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('person_id')->nullable();
            $table->integer('account_plan_id')->nullable();
            $table->double('initial_balance', 50, 2)->nullable();
            $table->string('description', 50)->nullable();
            $table->integer('user_id')->nullable();
            $table->enum('status', ['Activa', 'Inactiva'])->nullable()->default('Activa');
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
        Schema::dropIfExists('pretty_cash');
    }
};
