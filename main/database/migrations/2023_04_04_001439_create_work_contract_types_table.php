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
        Schema::create('work_contract_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('contract_terms_name_unique');
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->boolean('conclude')->default(false);
            $table->boolean('modified')->default(false);
            $table->string('description', 250)->nullable();
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
        Schema::dropIfExists('work_contract_types');
    }
};
