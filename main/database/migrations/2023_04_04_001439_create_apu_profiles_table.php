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
        Schema::create('apu_profiles', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('profile', 191)->nullable();
            $table->double('value_time_daytime_displacement', 50, 7)->nullable();
            $table->double('value_time_night_displacement', 50, 7)->nullable();
            $table->double('daytime_ordinary_hour_value', 50, 7)->nullable();
            $table->double('night_ordinary_hour_value', 50, 7)->nullable();
            $table->double('sunday_daytime_value', 50, 7)->nullable();
            $table->double('sunday_night_time_value', 50, 7)->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->integer('code')->nullable();
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
        Schema::dropIfExists('apu_profiles');
    }
};
