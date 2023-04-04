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
        Schema::create('work_order_blueprints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file');
            $table->string('general_set');
            $table->string('set_name');
            $table->boolean('predetermined');
            $table->unsignedInteger('work_order_id')->index('work_order_blueprints_work_order_id_foreign');
            $table->integer('person_id');
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
        Schema::dropIfExists('work_order_blueprints');
    }
};
