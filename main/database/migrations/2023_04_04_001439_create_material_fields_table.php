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
        Schema::create('material_fields', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('material_id')->nullable();
            $table->string('type', 100)->nullable();
            $table->string('property', 250)->nullable();
            $table->string('value', 50)->nullable();
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
        Schema::dropIfExists('material_fields');
    }
};
