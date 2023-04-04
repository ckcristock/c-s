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
        Schema::create('third_party_fields', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('label', 191)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('type', 191)->nullable();
            $table->enum('required', ['Si', 'No'])->nullable();
            $table->integer('length')->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->default('Activo');
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
        Schema::dropIfExists('third_party_fields');
    }
};
