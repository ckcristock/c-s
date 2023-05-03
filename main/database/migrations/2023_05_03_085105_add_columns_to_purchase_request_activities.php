<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPurchaseRequestActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_activities', function (Blueprint $table) {
            $table->enum('status', ['Creación','Edición', 'Cotización', 'Aprobación','Compra'])->nullable()->default('Creación');
            $table->integer('purchase_request_id')->nullable();
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
            $table->dropColumn('status');
            $table->dropColumn('purchase_request_id');
        });
    }
}
