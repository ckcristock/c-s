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
        Schema::create('Causal_No_Conforme', function (Blueprint $table) {
            $table->bigInteger('Id_Causal_No_Conforme', true);
            $table->string('Codigo', 100)->nullable();
            $table->string('Nombre', 100)->nullable();
            $table->text('Tratamiento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('causal_no_conforme');
    }
};
