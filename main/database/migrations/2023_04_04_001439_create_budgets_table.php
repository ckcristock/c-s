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
        Schema::create('budgets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 50)->default('0');
            $table->string('format_code', 50)->default('0');
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable()->default(0);
            $table->integer('destinity_id')->nullable()->default(0);
            $table->string('line')->nullable();
            $table->double('trm', 50, 2)->nullable();
            $table->string('project')->nullable();
            $table->string('observation')->nullable();
            $table->double('total_cop', 50, 2)->nullable();
            $table->double('total_usd', 50, 2)->nullable();
            $table->double('unit_value_prorrateado_cop', 50, 2)->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->double('unit_value_prorrateado_usd', 50, 2)->nullable();
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
        Schema::dropIfExists('budgets');
    }
};
