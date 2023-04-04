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
        Schema::create('applicants', function (Blueprint $table) {
            $table->bigIncrements('id')->index('Índice 1');
            $table->integer('job_id')->default(0);
            $table->string('name', 150)->nullable();
            $table->string('surname', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('passport', 150)->nullable();
            $table->tinyInteger('visa')->nullable();
            $table->integer('visaType_id')->nullable();
            $table->string('education')->nullable();
            $table->integer('experience_year')->nullable();
            $table->string('conveyance', 50)->nullable();
            $table->integer('driving_license_id')->nullable();
            $table->string('curriculum', 500)->nullable();
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
        Schema::dropIfExists('applicants');
    }
};
