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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_order');
            $table->integer('quotation_id');
            $table->date('delivery_date');
            $table->date('date');
            $table->integer('third_party_id');
            $table->integer('municipality_id');
            $table->integer('third_party_person_id')->default(0);
            $table->string('observation');
            $table->string('code');
            $table->string('description');
            $table->string('technical_requirements');
            $table->string('legal_requirements');
            $table->enum('status', ['inicial', 'ingenieria', 'diseño', 'espera_info', 'desarrollo', 'revision', 'espera_materiales', 'produccion', 'anulada'])->default('inicial');
            $table->enum('type', ['interna', 'externa'])->default('interna');
            $table->string('format_code', 50);
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
        Schema::dropIfExists('work_orders');
    }
};
