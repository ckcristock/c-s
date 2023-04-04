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
        Schema::create('budget_item_subitem_indirect_costs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('budget_item_subitem_id')->nullable();
            $table->integer('indirect_cost_id')->nullable();
            $table->double('value', 50, 2)->nullable();
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
        Schema::dropIfExists('budget_item_subitem_indirect_costs');
    }
};
