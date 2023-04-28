<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationPurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_purchase_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_request_id');
            $table->string('code');
            $table->string('format_code', 50);
            $table->bigInteger('Id_Proveedor')->nullable();            
            $table->double('total_price', 50, 2)->nullable();
            $table->enum('status', ['Pendiente', 'Cargada', 'Aprobada'])->nullable()->default('Pendiente');
            $table->string('file', 500)->nullable();
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
        Schema::dropIfExists('quotation_purchase_requests');
    }
}
