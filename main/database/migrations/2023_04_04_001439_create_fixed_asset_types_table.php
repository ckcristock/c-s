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
        Schema::create('fixed_asset_types', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 150)->nullable();
            $table->enum('category', ['Tangible', 'Intangible'])->nullable()->default('Tangible');
            $table->integer('useful_life_niif')->nullable();
            $table->float('annual_depreciation_percentage_niif', 20, 4)->nullable();
            $table->integer('useful_life_pcga')->nullable();
            $table->float('annual_depreciation_percentage_pcga', 20, 4)->nullable();
            $table->integer('niif_depreciation_account_plan_id')->nullable();
            $table->integer('pcga_depreciation_account_plan_id')->nullable();
            $table->integer('niif_account_plan_id')->nullable();
            $table->integer('pcga_account_plan_id')->nullable();
            $table->integer('niif_account_plan_credit_depreciation_id')->nullable();
            $table->integer('pcga_account_plan_credit_depreciation_id')->nullable();
            $table->integer('niif_account_plan_debit_depreciation_id')->nullable();
            $table->integer('pcga_account_plan_debit_depreciation_id')->nullable();
            $table->integer('consecutive')->nullable();
            $table->string('mantis', 50)->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
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
        Schema::dropIfExists('fixed_asset_types');
    }
};
