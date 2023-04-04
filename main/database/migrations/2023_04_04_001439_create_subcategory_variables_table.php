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
        Schema::create('subcategory_variables', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('subcategory_id')->nullable();
            $table->string('label')->nullable();
            $table->enum('type', ['text', 'number', 'date'])->nullable()->default('text');
            $table->string('required', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subcategory_variables');
    }
};
