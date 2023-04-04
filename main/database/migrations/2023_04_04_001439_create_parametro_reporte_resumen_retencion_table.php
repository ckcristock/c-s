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
        Schema::create('Parametro_Reporte_Resumen_Retencion', function (Blueprint $table) {
            $table->integer('Id_Parametro_Reporte_Resumen_Retencion', true);
            $table->integer('Id_Plan_Cuenta')->nullable();
            $table->string('Concepto', 250)->nullable();
            $table->string('Tipo_Retencion', 45)->nullable();
            $table->enum('Tipo_Valor', ['D', 'C', 'D-C', 'C-D', 'Saldo'])->nullable();
            $table->timestamp('Created_At')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro_reporte_resumen_retencion');
    }
};
