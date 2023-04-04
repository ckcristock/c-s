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
        Schema::create('cities', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 50);
            $table->string('country_id', 50);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->string('dane_code')->nullable();
            $table->string('dian_code')->nullable();
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
        Schema::dropIfExists('cities');
    }
};
