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
        Schema::create('Centro_Costo', function (Blueprint $table) {
            $table->integer('Id_Centro_Costo', true);
            $table->string('Nombre', 50)->nullable();
            $table->string('Codigo', 50)->nullable();
            $table->integer('Id_Centro_Padre')->nullable();
            $table->integer('Id_Tipo_Centro')->nullable();
            $table->integer('Valor_Tipo_Centro')->nullable();
            $table->string('Estado', 20)->default('Activo');
            $table->enum('Movimiento', ['Si', 'No'])->nullable()->default('No');
            $table->integer('company_id')->nullable();
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
        Schema::dropIfExists('centro_costo');
    }
};
