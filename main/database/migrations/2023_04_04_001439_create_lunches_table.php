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
        Schema::create('lunches', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('value')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('person_id');
            $table->integer('dependency_id');
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->enum('apply', ['Si', 'No'])->nullable();
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
        Schema::dropIfExists('lunches');
    }
};
