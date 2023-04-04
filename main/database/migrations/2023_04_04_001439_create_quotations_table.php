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
        Schema::create('quotations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('money_type', ['cop', 'usd'])->nullable()->default('cop');
            $table->integer('customer_id');
            $table->integer('third_party_person_id')->nullable();
            $table->string('format_code', 50)->default('');
            $table->integer('destinity_id');
            $table->float('trm', 10, 0);
            $table->string('description', 100);
            $table->string('included', 100);
            $table->string('observation', 500)->nullable();
            $table->double('total_cop')->default(0);
            $table->double('total_usd')->default(0);
            $table->dateTime('date');
            $table->string('code', 50)->nullable();
            $table->enum('status', ['Pendiente', 'Aprobada', 'No aprobada', 'Anulada'])->default('Pendiente');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->longText('commercial_terms')->nullable();
            $table->longText('legal_requirements')->nullable();
            $table->longText('technical_requirements')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};
