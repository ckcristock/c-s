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
        Schema::create('Plan_Cuentas', function (Blueprint $table) {
            $table->integer('Id_Plan_Cuentas', true);
            $table->string('Tipo_P', 60)->nullable();
            $table->string('Tipo_Niif', 60)->nullable();
            $table->string('Codigo', 100)->nullable()->index('Codigo_idx');
            $table->string('Codigo_Niif', 100)->nullable()->index('Codigo_Niif_idx');
            $table->string('Codigo_Padre', 100)->nullable();
            $table->string('Nombre', 100)->nullable();
            $table->string('Nombre_Niif', 100)->nullable();
            $table->string('Estado', 100)->nullable()->default('ACTIVO');
            $table->string('Naturaleza', 100)->nullable();
            $table->string('Movimiento', 100)->nullable();
            $table->string('Nombre_Banco', 50)->default('0');
            $table->string('Ajuste_Contable', 100)->nullable();
            $table->string('Cierra_Terceros', 100)->nullable();
            $table->string('Documento', 100)->nullable();
            $table->string('Base', 100)->nullable();
            $table->string('Valor', 100)->nullable();
            $table->decimal('Porcentaje', 6, 3)->nullable();
            $table->string('Centro_Costo', 100)->nullable();
            $table->string('Depreciacion', 100)->nullable();
            $table->string('Amortizacion', 100)->nullable();
            $table->string('Exogeno', 100)->nullable();
            $table->string('Maneja_Nit', 100)->nullable();
            $table->string('Cie_Anual', 45)->nullable();
            $table->string('Nit_Cierre', 45)->nullable();
            $table->string('Banco', 45)->nullable()->default('S');
            $table->string('Cod_Banco', 45)->nullable();
            $table->string('Nit', 45)->nullable();
            $table->enum('Clase_Cta', ['Corriente', 'Ahorros'])->nullable();
            $table->string('Cta_Numero', 45)->nullable();
            $table->string('Reporte', 45)->nullable();
            $table->string('Niif', 45)->nullable();
            $table->decimal('Porcentaje_Real', 6, 3)->nullable();
            $table->enum('Tipo_Cierre_Mensual', ['Sin Asignar', 'Ingresos', 'Costos', 'Gastos'])->default('Sin Asignar');
            $table->enum('Tipo_Cierre_Anual', ['Sin Asignar', 'Ingresos', 'Costos', 'Gastos'])->default('Sin Asignar');
            $table->integer('Id_Empresa')->default(1);
            $table->integer('company_id')->default(1);
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
        Schema::dropIfExists('plan_cuentas');
    }
};
