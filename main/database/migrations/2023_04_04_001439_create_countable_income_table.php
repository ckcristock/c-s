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
        Schema::create('countable_income', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('concept', 50);
            $table->enum('type', ['Constitutivo', 'No Constitutivo']);
            $table->string('accounting_account', 191)->nullable();
            $table->boolean('state')->default(true);
            $table->boolean('editable')->default(false);
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
        Schema::dropIfExists('countable_income');
    }
};
