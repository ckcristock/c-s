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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 191);
            $table->string('associated_account', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->bigInteger('balance')->nullable();
            $table->enum('status', ['Activo', 'Inactivo'])->nullable()->default('Activo');
            $table->enum('type', ['0', '1'])->nullable();
            $table->string('description', 250)->nullable();
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
        Schema::dropIfExists('bank_accounts');
    }
};
