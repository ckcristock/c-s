<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultPorcentajeInPlanCuentas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Plan_Cuentas', function (Blueprint $table) {
            $table->decimal('Porcentaje', 6, 3)->nullable(false)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Plan_Cuentas', function (Blueprint $table) {
            $table->decimal('Porcentaje', 6, 3)->nullable()->change();
        });
    }
}
