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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('quotation_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->double('value_cop')->nullable();
            $table->double('value_usd')->nullable();
            $table->double('total_cop')->nullable();
            $table->double('total_usd')->nullable();
            $table->double('cuantity')->nullable();
            $table->integer('quotationitemable_id')->nullable();
            $table->string('quotationitemable_type', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_items');
    }
};
