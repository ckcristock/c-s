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
        Schema::create('apu_part_raw_materials', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('geometry_id', 191)->nullable();
            $table->bigInteger('apu_part_id');
            $table->string('material_id', 191)->nullable();
            $table->double('weight_kg', 50, 2);
            $table->double('q', 50, 2);
            $table->double('weight_total', 50, 2);
            $table->double('value_kg', 50, 2);
            $table->double('total_value', 50, 2);
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
        Schema::dropIfExists('apu_part_raw_materials');
    }
};
