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
        Schema::create('dotation_products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('dotation_id')->nullable();
            $table->integer('inventary_dotation_id')->nullable();
            $table->integer('quantity');
            $table->double('cost', 50, 2)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dotation_products');
    }
};
