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
        Schema::create('Punto_Dispensacion', function (Blueprint $table) {
            $table->integer('Id_Punto_Dispensacion', true);
            $table->string('Nombre', 200)->nullable();
            $table->string('Tipo', 200)->nullable();
            $table->enum('Tipo_Entrega', ['Despacho', 'Radicacion'])->nullable();
            $table->integer('Departamento')->nullable()->index('Departamento_idx');
            $table->integer('Municipio')->nullable();
            $table->string('Direccion', 200)->nullable();
            $table->string('Telefono', 200)->nullable();
            $table->integer('Responsable')->nullable();
            $table->string('No_Pos', 200)->nullable();
            $table->string('Turnero', 200)->nullable();
            $table->integer('Cajas')->nullable();
            $table->enum('Wacom', ['Si', 'No']);
            $table->enum('Entrega_Formula', ['Si', 'No'])->default('No');
            $table->enum('Entrega_Doble', ['Si', 'No'])->default('No');
            $table->enum('Autorizacion', ['Si', 'No'])->default('No');
            $table->enum('Tipo_Dispensacion', ['Entrega', 'Digitacion', 'Cerrado'])->default('Entrega');
            $table->enum('Campo_Mipres', ['Numero_Prescripcion', 'Id_Direccionamiento'])->default('Numero_Prescripcion');
            $table->integer('Id_Bodega_Despacho')->nullable();
            $table->enum('Estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punto_dispensacion');
    }
};
