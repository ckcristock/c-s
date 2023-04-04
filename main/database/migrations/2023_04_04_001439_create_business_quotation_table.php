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
        Schema::create('business_quotation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('business_id')->nullable();
            $table->integer('quotation_id')->nullable();
            $table->enum('status', ['Aprobada', 'Pendiente', 'Rechazada'])->nullable()->default('Pendiente');
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
        Schema::dropIfExists('business_quotation');
    }
};
