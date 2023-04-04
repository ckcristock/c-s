<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_item_subitems', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('description', 100)->nullable();
            $table->double('cuantity')->nullable();
            $table->double('value_cop')->nullable();
            $table->double('value_usd')->nullable();
            $table->double('total_cop')->nullable();
            $table->double('total_usd')->nullable();
            $table->bigInteger('quotation_item_id')->nullable();
            $table->integer('quotationitemsubitemable_id')->nullable();
            $table->string('quotationitemsubitemable_type', 50)->nullable();
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
        Schema::dropIfExists('quotation_item_subitems');
    }
};
