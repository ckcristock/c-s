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
        Schema::create('cut_laser_material_values', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cut_laser_material_id')->default(0);
            $table->float('thickness', 50, 7)->nullable();
            $table->double('unit_value', 50, 7)->nullable();
            $table->double('actual_speed', 50, 7)->nullable();
            $table->double('seconds_percing', 1, 1)->nullable();
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
        Schema::dropIfExists('cut_laser_material_values');
    }
};
