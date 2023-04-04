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
        Schema::create('budget_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->double('total_cost', 50, 2)->default(0);
            $table->double('subtotal_indirect_cost', 50, 2)->default(0);
            $table->double('value_amd', 50, 2)->default(0);
            $table->double('value_unforeseen', 50, 2)->default(0);
            $table->double('value_utility', 50, 2)->default(0);
            $table->double('total_amd_imp_uti', 50, 2)->default(0);
            $table->double('another_values', 50, 2)->default(0);
            $table->double('subTotal', 50, 2)->default(0);
            $table->double('retention', 50, 2)->default(0);
            $table->double('percentage_sale', 50, 2)->default(0);
            $table->double('value_cop', 50, 2)->default(0);
            $table->double('value_usd', 50, 2)->default(0);
            $table->double('value_prorrota_cop', 50, 2)->default(0);
            $table->double('value_prorrota_usd', 50, 2)->default(0);
            $table->double('unit_value_prorrateado_cop', 50, 2)->default(0);
            $table->double('unit_value_prorrateado_usd', 50, 2)->default(0);
            $table->integer('budget_id')->default(0);
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
        Schema::dropIfExists('budget_items');
    }
};
