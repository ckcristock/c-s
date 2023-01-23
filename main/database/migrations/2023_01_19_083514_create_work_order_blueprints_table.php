<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderBlueprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_blueprints', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('general_set');
            $table->string('set_name');
            $table->boolean('predetermined');
            $table->unsignedInteger('work_order_id');
            $table->integer('person_id');
            $table->timestamps();
        });
        Schema::table('work_order_blueprints', function (Blueprint $table) {
            $table->foreign('work_order_id')
                ->references('id')->on('work_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_order_blueprints');
    }
}
