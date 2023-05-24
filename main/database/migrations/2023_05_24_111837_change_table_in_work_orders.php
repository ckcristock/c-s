<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTableInWorkOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('work_orders');
        Schema::create('work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 250);
            $table->string('name', 250);
            $table->string('referral_number', 250)->nullable();
            $table->string('invoice_number', 250)->nullable();
            $table->string('purchase_order', 255)->nullable();
            $table->enum('class', ['Interna', 'Proyecto', 'Repuesto', 'Servicio'])->default('Interna');
            $table->enum('type', ['V', 'I', 'G'])->default('V');
            $table->date('delivery_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('delivery_date_of_plans')->nullable();
            $table->date('date_of_plans_received')->nullable();
            $table->date('date_of_referral')->nullable();
            $table->date('date_of_invoice')->nullable();
            $table->double('value', 50, 2)->nullable();
            $table->unsignedBigInteger('third_party_id')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->unsignedBigInteger('third_party_person_id')->nullable();
            $table->text('observations')->nullable();
            $table->string('format_code', 250)->nullable();
            $table->longText('description')->nullable();
            $table->longText('technical_requirements')->nullable();
            $table->longText('legal_requirements')->nullable();
            $table->enum('status', ['inicial', 'ingenieria', 'diseño', 'espera_info', 'desarrollo', 'revision', 'espera_materiales', 'produccion', 'anulada'])->default('inicial');
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
        Schema::table('work_orders', function (Blueprint $table) {
            //
        });
    }
}
