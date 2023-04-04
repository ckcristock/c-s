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
        Schema::create('apu_set_internal_processes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('apu_set_id');
            $table->string('description', 100)->nullable();
            $table->string('unit_id', 100)->nullable();
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
        Schema::dropIfExists('apu_set_internal_processes');
    }
};
