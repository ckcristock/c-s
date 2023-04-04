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
        Schema::create('work_certificates', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('person_id');
            $table->string('file', 200)->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('information', 100)->nullable();
            $table->string('addressee', 100)->nullable();
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
        Schema::dropIfExists('work_certificates');
    }
};
