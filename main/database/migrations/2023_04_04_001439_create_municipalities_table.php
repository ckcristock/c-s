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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 200)->nullable();
            $table->string('abbreviation', 10)->nullable();
            $table->integer('department_id')->nullable();
            $table->string('code', 10)->nullable();
            $table->integer('dian_code')->nullable();
            $table->integer('dane_code')->nullable();
            $table->integer('municipalities_id');
            $table->double('percentage_product', 50, 7);
            $table->double('percentage_service', 50, 7);
            $table->enum('state', ['Activo', 'Inactivo'])->default('Activo');
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
        Schema::dropIfExists('municipalities');
    }
};
