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
        Schema::create('memorandums', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('person_id');
            $table->text('details')->nullable();
            $table->string('file', 500)->nullable();
            $table->enum('level', ['Leve', 'Grave'])->nullable();
            $table->enum('state', ['Pendiente', 'Aprobado', 'Legalizado'])->nullable()->default('Pendiente');
            $table->bigInteger('approve_user_id')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->bigInteger('memorandum_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memorandums');
    }
};
