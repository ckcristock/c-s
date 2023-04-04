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
        Schema::create('apu_part_others', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 100)->nullable();
            $table->string('unit_id', 100)->nullable();
            $table->bigInteger('apu_part_id');
            $table->double('q_unit', 50, 2);
            $table->double('q_total', 50, 2);
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
        Schema::dropIfExists('apu_part_others');
    }
};
