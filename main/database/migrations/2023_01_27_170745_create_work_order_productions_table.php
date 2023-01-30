<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_productions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('allocator_person_id');
            $table->unsignedInteger('work_order_id');
            $table->string('observations', 500);
            $table->integer('hours');
            $table->integer('minutes');
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->enum('status', ['pendiente', 'proceso', 'completado'])->default('pendiente');
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
        Schema::dropIfExists('work_order_productions');
    }
}
