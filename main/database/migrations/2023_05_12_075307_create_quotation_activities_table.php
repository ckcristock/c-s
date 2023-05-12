<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quotation_id');
            $table->string('icon', 50)->nullable();
            $table->string('title', 50)->nullable();
            $table->unsignedBigInteger('person_id');
            $table->text('description')->nullable();
            $table->enum('status', ['Creación', 'Edición', 'Aprobación', 'Anulación', 'Devuelta'])->nullable();
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
        Schema::dropIfExists('quotation_activities');
    }
}
