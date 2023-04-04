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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('usuario', 100)->nullable();
            $table->integer('person_id')->nullable();
            $table->json('menu')->nullable();
            $table->rememberToken();
            $table->string('password', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->integer('change_password')->nullable()->default(1);
            $table->dateTime('password_updated_at')->nullable();
            $table->enum('state', ['Activo', 'Inactivo']);
            $table->integer('board_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
