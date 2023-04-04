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
        Schema::create('Cheque_Consecutivo', function (Blueprint $table) {
            $table->integer('Id_Cheque_Consecutivo', true);
            $table->integer('Id_Plan_Cuentas')->nullable();
            $table->string('Prefijo', 45)->nullable();
            $table->integer('Inicial')->nullable();
            $table->integer('Final')->nullable();
            $table->integer('Consecutivo')->nullable()->default(1);
            $table->string('Estado', 45)->nullable()->default('Activo');
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
        Schema::dropIfExists('cheque_consecutivo');
    }
};
