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
        Schema::create('apu_part_cut_laser', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_id', 100)->nullable();
            $table->bigInteger('apu_part_id');
            $table->bigInteger('thickness')->nullable();
            $table->string('sheets_amount', 100)->nullable();
            $table->double('long', 50, 2);
            $table->double('width', 50, 2);
            $table->double('total_length', 50, 2);
            $table->double('amount_holes', 50, 2);
            $table->double('diameter', 50, 2);
            $table->double('total_hole_perimeter', 50, 2);
            $table->double('time', 50, 2);
            $table->double('minute_value', 50, 2);
            $table->double('value', 50, 2);
            $table->integer('cut_laser_material_id')->nullable();
            $table->integer('cut_laser_material_value_id')->nullable();
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
        Schema::dropIfExists('apu_part_cut_laser');
    }
};
