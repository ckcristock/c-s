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
        Schema::create('apu_part_raw_material_measures', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('measure_id', 100)->nullable();
            $table->double('value', 50, 2);
            $table->string('apu_part_raw_material_id', 100)->nullable();
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
        Schema::dropIfExists('apu_part_raw_material_measures');
    }
};
