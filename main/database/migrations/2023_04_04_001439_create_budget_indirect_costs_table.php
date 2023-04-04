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
        Schema::create('budget_indirect_costs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('indirect_cost_id')->nullable()->default(0);
            $table->double('percentage', 50, 2)->nullable();
            $table->integer('budget_id')->nullable();
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
        Schema::dropIfExists('budget_indirect_costs');
    }
};
