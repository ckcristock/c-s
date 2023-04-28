<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterEnumStatusInQuotationPurchaseRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_purchase_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE quotation_purchase_requests MODIFY COLUMN status ENUM('Pendiente', 'Aprobada', 'Rechazada') DEFAULT 'Pendiente'");
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
        });
    }
}
