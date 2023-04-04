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
        Schema::create('electronic_payrolls', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('status', 50)->nullable();
            $table->integer('person_payroll_payment_id')->nullable();
            $table->string('cune')->nullable();
            $table->string('errors', 800)->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('electronic_payrolls');
    }
};
