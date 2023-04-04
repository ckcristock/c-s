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
        Schema::create('center_costs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 50);
            $table->string('code', 50);
            $table->integer('parent_center_id');
            $table->integer('center_type_id');
            $table->integer('center_type_value');
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->enum('movement', ['Si', 'No']);
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
        Schema::dropIfExists('center_costs');
    }
};
