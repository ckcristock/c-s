<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEnumStatusInProductPurchaseRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_purchase_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE product_purchase_requests MODIFY COLUMN status ENUM('Pendiente', 'Cotizaciones cargadas', 'Cotización Aprobada') DEFAULT 'Pendiente'");
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
            //
        });
    }
}
