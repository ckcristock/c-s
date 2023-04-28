<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseIdInQuotationPurchaseRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id')->nullable()->before('product_purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('purchase_request_id');
        });
    }
}
