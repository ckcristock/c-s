<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_id');
            $table->unsignedBigInteger('person_id');
            $table->string('identifier');
            $table->string('fullname');
            $table->integer('worked_days');
            $table->double('amount', 8,2);
            $table->string('lapse');
            $table->double('average_amount', 8, 2);
            $table->dateTime('payment_date');
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
        Schema::dropIfExists('bonus_people');
    }
}
