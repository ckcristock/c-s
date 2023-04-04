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
        Schema::create('countable_deductions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('concept', 191);
            $table->string('accounting_account', 191);
            $table->boolean('state')->default(true);
            $table->boolean('editable')->default(true);
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
        Schema::dropIfExists('countable_deductions');
    }
};
