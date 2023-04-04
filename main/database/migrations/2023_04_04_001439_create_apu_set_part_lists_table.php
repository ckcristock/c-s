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
        Schema::create('apu_set_part_lists', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('apu_set_id');
            $table->unsignedInteger('apu_part_id')->default(0);
            $table->unsignedInteger('apu_set_child_id')->default(0);
            $table->string('apu_type', 50)->nullable();
            $table->bigInteger('unit_id');
            $table->bigInteger('amount')->nullable();
            $table->double('unit_cost', 50, 2);
            $table->double('total', 50, 2);
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
        Schema::dropIfExists('apu_set_part_lists');
    }
};
