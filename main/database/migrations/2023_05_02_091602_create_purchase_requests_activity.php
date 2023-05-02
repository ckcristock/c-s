<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requests_activity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_purchase_request')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->dateTime('Fecha')->nullable();
            $table->text('Detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requests_activity');
    }
}
