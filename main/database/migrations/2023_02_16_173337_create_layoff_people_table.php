<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayoffPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layoff_people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('layoffs_id');
            $table->unsignedBigInteger('person_id');
            $table->string('identifier');
            $table->string('fullname');
            $table->integer('worked_days');
            $table->string('period');
            $table->float('salary_basic');
            $table->float('avg_salary_basic');
            $table->float('total');
            $table->date('payment_date');
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
        Schema::dropIfExists('layoff_people');
    }
}
