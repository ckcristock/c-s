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
        Schema::create('pay_vacations', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('payroll_factor_id');
            $table->integer('days');
            $table->enum('state', ['Pago', 'No Pagado'])->default('No Pagado');
            $table->float('value', 10, 0)->default(0);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_vacations');
    }
};
