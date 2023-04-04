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
        Schema::create('retention_types', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 50)->nullable();
            $table->integer('account_plan_id')->nullable();
            $table->float('percentage', 20, 4)->nullable();
            $table->string('description', 250)->nullable();
            $table->enum('state', ['Activo', 'Inactivo'])->nullable()->default('Activo');
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
        Schema::dropIfExists('retention_types');
    }
};
