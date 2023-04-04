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
        Schema::create('egress_types', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 191);
            $table->string('associated_account', 191);
            $table->enum('type', ['Prestamo', 'Deducción']);
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
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
        Schema::dropIfExists('egress_types');
    }
};
