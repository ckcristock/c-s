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
        Schema::create('apu_services', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 500)->nullable();
            $table->string('format_code', 100)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('line', 191)->nullable();
            $table->string('observation', 500)->nullable();
            $table->integer('person_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('third_party_id')->nullable();
            $table->double('administrative_percentage', 50, 7)->nullable();
            $table->double('administrative_value', 50, 7)->nullable();
            $table->double('unforeseen_percentage', 50, 7)->nullable();
            $table->double('unforeseen_value', 50, 7)->nullable();
            $table->double('utility_percentage', 50, 7)->nullable();
            $table->double('general_subtotal_travel_expense_labor', 50, 7)->nullable();
            $table->double('general_subtotal_travel_expense_labor_c', 50, 7)->nullable();
            $table->double('sale_price_cop_withholding_total', 50, 7)->nullable();
            $table->double('sale_price_usd_withholding_total', 50, 7)->nullable();
            $table->double('subtotal_administrative_unforeseen', 50, 7)->nullable();
            $table->double('subtotal_administrative_unforeseen_utility', 50, 7)->nullable();
            $table->double('subtotal_assembly_commissioning', 50, 7)->nullable();
            $table->double('subtotal_accompaniment', 50, 7)->nullable();
            $table->double('subtotal_dimensional_validation', 50, 7)->nullable();
            $table->double('subtotal_dimensional_validation_c', 50, 7)->nullable();
            $table->double('subtotal_assembly_c', 50, 7)->nullable();
            $table->double('subtotal_accompaniment_c', 50, 7)->nullable();
            $table->double('subtotal_labor', 50, 7)->nullable();
            $table->double('subtotal_labor_mpm', 50, 7)->nullable();
            $table->double('subtotal_labor_apm', 50, 7)->nullable();
            $table->double('subtotal_travel_expense', 50, 7)->nullable();
            $table->double('subtotal_travel_expense_mpm', 50, 7)->nullable();
            $table->double('subtotal_travel_expense_apm', 50, 7)->nullable();
            $table->double('subtotal_travel_expense_vd_c', 50, 7)->nullable();
            $table->double('subtotal_travel_expense_me_c', 50, 7)->nullable();
            $table->double('subtotal_travel_expense_apm_c', 50, 7)->nullable();
            $table->double('trm', 50, 7)->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->timestamps();
            $table->string('typeapu_name', 50)->default('Servicio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apu_services');
    }
};
