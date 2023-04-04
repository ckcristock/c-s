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
        Schema::create('payroll_risks_arls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prefix', 191);
            $table->string('concept', 191);
            $table->decimal('percentage', 4);
            $table->timestamps();
            $table->string('account_plan_id', 191)->nullable();
            $table->string('account_setoff', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_risks_arls');
    }
};
