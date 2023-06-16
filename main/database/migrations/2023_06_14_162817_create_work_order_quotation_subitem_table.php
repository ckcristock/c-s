<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderQuotationSubitemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_quotation_subitems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_item_subitem_id')->nullable();
            $table->unsignedBigInteger('work_order_quotation_item_id');
            $table->longText('name');
            $table->bigInteger('cuantity')->default(0);
            $table->string('unit', 255);
            $table->longText('observations')->nullable();
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
        Schema::dropIfExists('work_order_quotation_subitems');
    }
}
