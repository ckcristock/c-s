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
        Schema::create('Orden_Compra_Internacional', function (Blueprint $table) {
            $table->bigInteger('Id_Orden_Compra_Internacional', true);
            $table->string('Codigo', 10)->nullable();
            $table->integer('Identificacion_Funcionario')->nullable()->default(0);
            $table->timestamp('Fecha_Registro')->nullable()->useCurrent();
            $table->dateTime('Fecha')->nullable();
            $table->bigInteger('Id_Bodega')->nullable();
            $table->integer('Id_Bodega_Nuevo')->nullable();
            $table->bigInteger('Id_Proveedor')->nullable();
            $table->text('Observaciones')->nullable();
            $table->string('Puerto_Destino', 60);
            $table->double('Tasa_Dolar', 20, 2);
            $table->string('Tipo', 30)->nullable();
            $table->enum('Estado', ['Pendiente', 'Recibida', 'Anulada', ''])->nullable()->default('Pendiente');
            $table->string('Codigo_Qr', 100)->default('');
            $table->double('Flete_Internacional', 20, 2)->nullable()->default(0)->comment('Este valor esta expresado en dolares (USD)');
            $table->double('Seguro_Internacional', 20, 2)->default(0)->comment('Este valor esta expresado en dolares (USD)');
            $table->double('Tramite_Sia', 20, 2)->default(0)->comment('Este valor esta expresado en pesos colombianos');
            $table->double('Flete_Nacional', 20, 2)->default(0)->comment('Este valor esta expresado en  pesos colombianos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_compra_internacional');
    }
};
