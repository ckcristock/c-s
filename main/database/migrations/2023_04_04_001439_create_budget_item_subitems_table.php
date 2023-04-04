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
        Schema::create('budget_item_subitems', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('budget_item_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('type_module', 50)->nullable();
            $table->string('description', 50)->nullable();
            $table->integer('apu_part_id')->nullable();
            $table->integer('apu_set_id')->nullable();
            $table->integer('apu_service_id')->nullable();
            $table->integer('cuantity')->nullable();
            $table->double('unit_cost', 50, 2)->nullable();
            $table->double('total_cost', 50, 2)->nullable();
            $table->double('subtotal_indirect_cost', 50, 2)->nullable();
            $table->double('percentage_amd', 50, 2)->nullable();
            $table->double('percentage_unforeseen', 50, 2)->nullable();
            $table->double('percentage_utility', 50, 2)->nullable();
            $table->double('value_amd', 50, 2)->nullable();
            $table->double('value_unforeseen', 50, 2)->nullable();
            $table->double('value_utility', 50, 2)->nullable();
            $table->double('total_amd_imp_uti', 50, 2)->nullable();
            $table->double('another_values', 50, 2)->nullable();
            $table->double('subTotal', 50, 2)->nullable();
            $table->double('retention', 50, 2)->nullable();
            $table->double('percentage_sale', 50, 2)->nullable();
            $table->double('value_cop', 50, 2)->nullable();
            $table->double('value_usd', 50, 2)->nullable();
            $table->double('unit_value_cop', 50, 2)->nullable();
            $table->double('unit_value_usd', 50, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->double('value_prorrota_cop', 50, 2)->nullable();
            $table->double('value_prorrota_usd', 50, 2)->nullable();
            $table->double('unit_value_prorrateado_cop', 50, 2)->nullable();
            $table->double('unit_value_prorrateado_usd', 50, 2)->nullable();
            $table->string('observation')->nullable();
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
        Schema::dropIfExists('budget_item_subitems');
    }
};
