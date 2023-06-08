<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalsInWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('purchase_order');
            $table->double('total_budgets', 50, 2)->nullable();
            $table->double('total_apu_parts', 50, 2)->nullable();
            $table->double('total_apu_sets', 50, 2)->nullable();
            $table->double('total_apu_services', 50, 2)->nullable();
            $table->double('total_budget_part_set_service', 50, 2)->nullable();
            $table->double('total_order_managment', 50, 2)->nullable();
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
