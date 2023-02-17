<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayoffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layoffs', function (Blueprint $table) {
            $table->id();
            $table->string('descirpcion');
            $table->float('total');
            $table->integer('total_employees');
            $table->string('period');
            $table->date('payment_day');
            $table->string('status');
            $table->string('payer');
            $table->unsignedBigInteger('payer_id');
            $table->string('payer_fullname');
            $table->longText('observations');
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
        Schema::dropIfExists('layoffs');
    }
}
