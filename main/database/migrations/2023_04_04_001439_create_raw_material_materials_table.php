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
        Schema::create('raw_material_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('material_id')->nullable();
            $table->unsignedInteger('kg_value')->nullable();
            $table->float('density', 10, 0)->unsigned()->nullable();
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
        Schema::dropIfExists('raw_material_materials');
    }
};
