<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameAndAmountColumnsToProductPurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_purchase_requests', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->integer('ammount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('ammount');
        });
    }
}
