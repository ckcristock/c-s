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
        Schema::create('Retencion', function (Blueprint $table) {
            $table->integer('Id_Retencion', true);
            $table->string('Nombre', 150)->nullable();
            $table->integer('Id_Plan_Cuenta')->nullable()->index('Id_Plan_Cuenta_idx');
            $table->double('Porcentaje')->nullable();
            $table->string('Estado', 45)->nullable()->default('Activo');
            $table->longText('Descripcion')->nullable();
            $table->enum('Tipo_Retencion', ['Renta', 'Iva', 'Ica', ''])->default('Renta');
            $table->enum('Modalidad_Retencion', ['Compras', 'Ventas', 'General', ''])->default('General');
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
        Schema::dropIfExists('retencion');
    }
};
