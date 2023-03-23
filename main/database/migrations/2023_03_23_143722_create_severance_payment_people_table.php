<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeverancePaymentPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('severance_payment_people', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('severance_payment_id');
            $table->unsignedInteger('people_id');
            $table->bigInteger('total');
            $table->unsignedInteger('company_id')->default(1);
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
        Schema::dropIfExists('severance_payment_people');
    }
}
