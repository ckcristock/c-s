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
        Schema::create('apu_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 191);
            $table->string('name', 191)->nullable();
            $table->string('city_id', 191)->nullable();
            $table->integer('person_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('third_party_id');
            $table->string('line', 191);
            $table->bigInteger('minute_value_laser')->default(0);
            $table->bigInteger('minute_value_water')->default(0);
            $table->integer('amount')->nullable();
            $table->string('observation', 200)->nullable();
            $table->integer('subtotal_raw_material')->nullable();
            $table->integer('commercial_materials_subtotal')->nullable();
            $table->integer('cut_water_total_amount')->nullable();
            $table->integer('cut_water_unit_subtotal')->nullable();
            $table->integer('cut_water_subtotal')->nullable();
            $table->integer('cut_laser_total_amount')->nullable();
            $table->integer('cut_laser_unit_subtotal')->nullable();
            $table->integer('cut_laser_subtotal')->nullable();
            $table->integer('machine_tools_subtotal')->nullable();
            $table->integer('internal_proccesses_subtotal')->nullable();
            $table->integer('external_proccesses_subtotal')->nullable();
            $table->integer('others_subtotal')->nullable();
            $table->integer('total_direct_cost')->nullable();
            $table->integer('unit_direct_cost')->nullable();
            $table->integer('indirect_cost_total')->nullable();
            $table->integer('direct_costs_indirect_costs_total')->nullable();
            $table->integer('direct_costs_indirect_costs_unit')->nullable();
            $table->integer('administrative_percentage')->nullable();
            $table->integer('administrative_value')->nullable();
            $table->integer('unforeseen_percentage')->nullable();
            $table->integer('unforeseen_value')->nullable();
            $table->integer('administrative_Unforeseen_subTotal')->nullable();
            $table->integer('administrative_Unforeseen_unit')->nullable();
            $table->integer('utility_percentage')->nullable();
            $table->integer('admin_unforeseen_utility_subTotal')->nullable();
            $table->integer('admin_unforeseen_utility_unit')->nullable();
            $table->integer('sale_price_cop_withholding_total')->nullable();
            $table->integer('sale_value_cop_unit')->nullable();
            $table->integer('trm')->nullable();
            $table->integer('sale_price_usd_withholding_total')->nullable();
            $table->integer('sale_value_usd_unit')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('typeapu_name', 50)->default('Pieza');
            $table->string('format_code', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apu_parts');
    }
};
