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
        Schema::create('business_budgets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('budget_id');
            $table->integer('business_id');
            $table->enum('status', ['Pendiente', 'Rechazado', 'Aprobado'])->nullable()->default('Pendiente');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_budgets');
    }
};
