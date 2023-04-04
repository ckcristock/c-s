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
        Schema::create('apu_part_cut_water', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_id', 100)->nullable();
            $table->bigInteger('apu_part_id');
            $table->bigInteger('thickness_id');
            $table->string('amount', 100)->nullable();
            $table->double('long', 50, 2);
            $table->double('width', 50, 2);
            $table->double('total_length', 50, 2);
            $table->double('amount_cut', 50, 2);
            $table->double('diameter', 50, 2);
            $table->double('total_hole_perimeter', 50, 2);
            $table->double('time', 50, 2);
            $table->double('minute_value', 50, 2);
            $table->double('value', 50, 2);
            $table->double('thickness_value', 50, 7);
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
        Schema::dropIfExists('apu_part_cut_water');
    }
};
