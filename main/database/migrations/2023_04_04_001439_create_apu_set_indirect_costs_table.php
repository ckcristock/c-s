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
        Schema::create('apu_set_indirect_costs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('apu_set_id');
            $table->string('name', 100)->nullable();
            $table->double('percentage', 50, 2);
            $table->double('value', 50, 2);
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
        Schema::dropIfExists('apu_set_indirect_costs');
    }
};
