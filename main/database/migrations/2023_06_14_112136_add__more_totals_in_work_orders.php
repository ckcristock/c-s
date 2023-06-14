<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreTotalsInWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->double('total_indirect_cost_budgets', 50, 2)->nullable();
            $table->double('total_direct_cost_budgets', 50, 2)->nullable();
            $table->double('total_indirect_cost_apu_services', 50, 2)->nullable();
            $table->double('total_direct_cost_apu_services', 50, 2)->nullable();
            $table->double('total_indirect_cost_apu_parts', 50, 2)->nullable();
            $table->double('total_direct_cost_apu_parts', 50, 2)->nullable();
            $table->double('total_indirect_cost_apu_sets', 50, 2)->nullable();
            $table->double('total_direct_cost_apu_sets', 50, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            //
        });
    }
}
