<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromProductPurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('quantity_of_products');
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
            $table->enum('status', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->integer('quantity_of_products')->default(0);
        });
    }
}
