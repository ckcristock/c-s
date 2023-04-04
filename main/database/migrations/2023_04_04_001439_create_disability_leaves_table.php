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
        Schema::create('disability_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->string('concept', 191);
            $table->string('accounting_account', 191);
            $table->boolean('sum')->default(false);
            $table->boolean('state')->default(true);
            $table->string('novelty', 191)->nullable();
            $table->string('modality', 191)->nullable();
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
        Schema::dropIfExists('disability_leaves');
    }
};
