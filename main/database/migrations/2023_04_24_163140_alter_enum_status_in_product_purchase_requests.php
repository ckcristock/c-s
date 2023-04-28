<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterEnumStatusInProductPurchaseRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_purchase_requests', function (Blueprint $table) {
            $table->enum('status', ['Pendiente', 'Cotizaciones cargadas'])->default('Pendiente')->after('purchase_request_id');
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
            $table->dropColumn('status');
        });
    }
}
