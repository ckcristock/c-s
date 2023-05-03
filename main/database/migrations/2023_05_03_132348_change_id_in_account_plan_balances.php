<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdInAccountPlanBalances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_plan_balances', function (Blueprint $table) {
            $table->dropPrimary('id');
            $table->dropColumn('id');
        });
        Schema::table('account_plan_balances', function (Blueprint $table) {
            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_plan_balances', function (Blueprint $table) {
            //
        });
    }
}
