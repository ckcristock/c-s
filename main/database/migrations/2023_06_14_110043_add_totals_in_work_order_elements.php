<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalsInWorkOrderElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order_elements', function (Blueprint $table) {
            $table->double('total_indirect_cost', 50, 2)->nullable();
            $table->double('total_direct_cost', 50, 2)->nullable();
            $table->double('total', 50, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_order_elements', function (Blueprint $table) {
            //
        });
    }
}
