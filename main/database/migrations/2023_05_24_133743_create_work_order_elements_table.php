<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_elements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('work_orderable_id');
            $table->string('work_orderable_type', 250);
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
        Schema::dropIfExists('work_order_elements');
    }
}
