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
        Schema::create('Borrador_Contabilidad', function (Blueprint $table) {
            $table->integer('Id_Borrador_Contabilidad', true);
            $table->string('Codigo', 45)->nullable();
            $table->string('Tipo_Comprobante', 45)->nullable();
            $table->integer('Identificacion_Funcionario')->nullable();
            $table->text('Datos')->nullable();
            $table->string('Estado', 45)->nullable()->default('Activa');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrador_contabilidad');
    }
};
