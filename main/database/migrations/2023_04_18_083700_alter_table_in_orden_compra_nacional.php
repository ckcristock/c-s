<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInOrdenCompraNacional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Orden_Compra_Nacional', function (Blueprint $table) {
            $table->dropColumn('Tipo_Bodega');
            $table->dropColumn('Id_Punto_Dispensacion');
            $table->dropColumn('Id_Bodega');
            $table->dropColumn('Fecha_Creacion_Compra');
            $table->dropColumn('Fecha');
            $table->date('Fecha_Entrega_Real')->nullable()->after('Fecha_Entrega_Probable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Orden_Compra_Nacional', function (Blueprint $table) {
            $table->enum('Tipo_Bodega', ['Bodega', 'Punto'])->default('Bodega')->nullable();
            $table->integer('Id_Punto_Dispensacion')->nullable();
            $table->bigInteger('Id_Bodega')->nullable();
            $table->timestamp('Fecha_Creacion_Compra');
            $table->timestamp('Fecha');
            $table->dropColumn('Fecha_Entrega_Real');
        });
    }
}
