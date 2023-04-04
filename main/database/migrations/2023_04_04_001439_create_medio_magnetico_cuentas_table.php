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
        Schema::create('Medio_Magnetico_Cuentas', function (Blueprint $table) {
            $table->integer('Id_Medio_Magnetico_Cuentas', true);
            $table->integer('Id_Medio_Magnetico')->nullable();
            $table->integer('Id_Plan_Cuenta')->nullable();
            $table->string('Concepto', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medio_magnetico_cuentas');
    }
};
