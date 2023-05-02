<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseRequestActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_activities', function (Blueprint $table) {
            $table->dateTime('date')->nullable();
            $table->text('details')->nullable();
            $table->enum('status', ['Creación', 'Cotización', 'Aprobación', 'Compra'])->nullable()->default('Creación');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_activities', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('details');
            $table->dropColumn('status');
        });
    }
}
