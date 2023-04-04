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
        Schema::create('Balance_Inicial_Activo_Fijo', function (Blueprint $table) {
            $table->integer('Id_Balance_Inicial_Activo_Fijo');
            $table->integer('Id_Activo_Fijo')->nullable();
            $table->date('Fecha')->nullable();
            $table->integer('Vida_Util_Restante_PCGA')->nullable();
            $table->integer('Vida_Util_Restante_NIIF')->nullable();
            $table->decimal('Depreciacion_Acum_PCGA', 20)->nullable();
            $table->decimal('Depreciacion_Acum_NIIF', 20)->nullable();
            $table->decimal('Saldo_PCGA', 20)->nullable();
            $table->decimal('Saldo_NIIF', 20)->nullable();
            $table->string('Estado', 45)->nullable()->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_inicial_activo_fijo');
    }
};
