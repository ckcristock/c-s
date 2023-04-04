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
        Schema::create('apu_sets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 191)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('format_code', 50)->nullable();
            $table->string('city_id', 191)->nullable();
            $table->integer('person_id')->nullable();
            $table->integer('third_party_id');
            $table->string('line', 191);
            $table->string('observation')->nullable();
            $table->integer('list_pieces_sets_subtotal');
            $table->integer('machine_tools_subtotal');
            $table->integer('internal_processes_subtotal');
            $table->integer('external_processes_subtotal');
            $table->integer('others_subtotal')->nullable();
            $table->integer('total_direct_cost')->nullable();
            $table->integer('unit_direct_cost')->nullable();
            $table->integer('indirect_cost_total')->nullable();
            $table->integer('direct_costs_indirect_costs_total')->nullable();
            $table->integer('administrative_percentage')->nullable();
            $table->integer('administrative_value')->nullable();
            $table->integer('unforeseen_percentage')->nullable();
            $table->integer('unforeseen_value')->nullable();
            $table->integer('administrative_unforeseen_subtotal')->nullable();
            $table->integer('administrative_unforeseen_unit')->nullable();
            $table->integer('utility_percentage')->nullable();
            $table->integer('admin_unforeseen_utility_subtotal')->nullable();
            $table->integer('sale_price_cop_withholding_total')->nullable();
            $table->integer('trm')->nullable();
            $table->integer('sale_price_usd_withholding_total')->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('typeapu_name', 50)->default('Conjunto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apu_sets');
    }
};
