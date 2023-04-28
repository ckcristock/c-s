<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignInQuotationPurchaseRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('purchase_request_id');
            $table->dropColumn('Id_Proveedor');
            $table->unsignedBigInteger('third_party_id')->nullable()->after('format_code');
            $table->unsignedBigInteger('product_purchase_request_id')->nullable()->after('id');
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
            $table->dropColumn('product_purchase_request_id');
            $table->dropColumn('third_party_id');
            $table->unsignedBigInteger('Id_Proveedor')->nullable()->after('format_code');
            $table->unsignedBigInteger('purchase_request_id')->nullable()->after('id');
        });
    }
}
