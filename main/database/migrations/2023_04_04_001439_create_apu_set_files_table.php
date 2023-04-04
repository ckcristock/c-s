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
        Schema::create('apu_set_files', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('file', 100)->nullable();
            $table->string('apu_set_id', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('type', 100)->nullable();
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
        Schema::dropIfExists('apu_set_files');
    }
};
