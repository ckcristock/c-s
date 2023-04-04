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
        Schema::create('work_contracts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('position_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->tinyInteger('liquidated')->nullable()->default(0);
            $table->integer('person_id')->nullable();
            $table->double('salary', 50, 2)->nullable();
            $table->enum('turn_type', ['Rotativo', 'Fijo'])->nullable();
            $table->integer('fixed_turn_id')->nullable();
            $table->date('date_of_admission')->nullable();
            $table->integer('work_contract_type_id')->nullable();
            $table->integer('contract_term_id')->nullable();
            $table->date('date_end')->nullable();
            $table->date('old_date_end')->nullable();
            $table->timestamps();
            $table->integer('rotating_turn_id')->nullable();
            $table->tinyInteger('transport_assistance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_contracts');
    }
};
