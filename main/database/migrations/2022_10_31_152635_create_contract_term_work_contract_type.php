<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractTermWorkContractType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_term_work_contract_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_contract_type_id');
            $table->unsignedBigInteger('contract_term_id');
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
        Schema::dropIfExists('contract_term_work_contract_type');
    }
}
