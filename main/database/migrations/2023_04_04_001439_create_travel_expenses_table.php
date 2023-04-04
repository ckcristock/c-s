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
        Schema::create('travel_expenses', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('person_id');
            $table->bigInteger('user_id');
            $table->bigInteger('approve_user_id');
            $table->string('origin_id', 50);
            $table->string('destinity_id', 50);
            $table->string('travel_type', 50);
            $table->date('departure_date');
            $table->date('arrival_date');
            $table->integer('n_nights')->nullable();
            $table->double('baggage_usd', 50, 2)->nullable();
            $table->double('baggage_cop', 50, 2)->nullable();
            $table->double('total_hotels_usd', 50, 2)->nullable();
            $table->double('total_hotels_cop', 50, 2)->nullable();
            $table->double('total_transports_cop', 50, 2)->nullable();
            $table->double('total_taxis_usd', 50, 2)->nullable();
            $table->double('total_taxis_cop', 50, 2)->nullable();
            $table->double('total_feedings_usd', 50, 2)->nullable();
            $table->double('total_feedings_cop', 50, 2)->nullable();
            $table->double('total_laundry_cop', 50, 2)->nullable();
            $table->double('total_laundry_usd', 50, 2)->nullable();
            $table->double('other_expenses_usd', 50, 2)->nullable();
            $table->double('other_expenses_cop', 50, 2)->nullable();
            $table->double('total_usd', 50, 2)->nullable();
            $table->double('total_cop', 50, 2)->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->double('total', 50, 2);
            $table->integer('work_order_id')->nullable();
            $table->string('observation', 250)->nullable();
            $table->enum('state', ['Pendiente', 'Aprobado', 'Legalizado', 'Activo', 'Inactivo'])->nullable()->default('Pendiente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_expenses');
    }
};
