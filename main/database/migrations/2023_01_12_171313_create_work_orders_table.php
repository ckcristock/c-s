<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_order');
            $table->integer('quotation_id');
            $table->date('delivery_date');
            $table->date('date');
            $table->integer('third_party_id');
            $table->integer('municipality_id');
            $table->string('required_by');
            $table->string('observation');
            $table->string('code');
            $table->string('description');
            $table->string('technical_requirements');
            $table->string('legal_requirements');
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
        Schema::dropIfExists('work_orders');
    }
}
