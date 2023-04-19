<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Producto', function (Blueprint $table) {
            $table->dropColumn('Principio_Activo');
            $table->dropColumn('Familia');
            $table->dropColumn('Codigo_Cum');
            $table->dropColumn('Laboratorio_Comercial');
            $table->dropColumn('ATC');
            $table->dropColumn('Cantidad_Maxima');
            $table->dropColumn('Cantidad_Minima');
            $table->dropColumn('Invima');
            $table->dropColumn('Descripcion_ATC');
            $table->dropColumn('Fecha_Expedicion_Invima');
            $table->dropColumn('Precio_Minimo');
            $table->dropColumn('Precio_Maximo');
            $table->dropColumn('Fecha_Vencimiento_Invima');
            $table->dropColumn('Tipo_Regulacion');
            $table->dropColumn('Via_Administracion');
            $table->dropColumn('Tipo_Pos');
            $table->dropColumn('Regulado');
            $table->dropColumn('Laboratorio_Generico');
            $table->dropColumn('Peso_Presentacion_Regular');
            $table->dropColumn('Peso_Presentacion_Maxima');
            $table->dropColumn('Peso_Presentacion_Minima');
            $table->dropColumn('Concentracion');
            $table->dropColumn('Mantis');
            $table->dropColumn('Cantidad_Presentacion');
            $table->dropColumn('Nombre_Listado');
            $table->dropColumn('RotativoC');
            $table->dropColumn('RotativoD');
            $table->dropColumn('Tolerancia');
            $table->dropColumn('Actualizado');
            $table->dropColumn('Unidad_Empaque');
            $table->dropColumn('Porcentaje_Arancel');
            $table->dropColumn('Forma_Farmaceutica');
            $table->dropColumn('Estado_DIAN_Covid19');
            $table->dropColumn('CantUnMinDis');
            $table->dropColumn('Tipo_Catalogo');
            $table->dropColumn('Orden_Compra');
            $table->dropColumn('Ubicar');
            $table->dropColumn('Producto_Dotation_Type_Id');
            $table->dropColumn('Tipo');
            $table->dropColumn('Id_Tipo_Activo_Fijo');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Producto', function (Blueprint $table) {
            //
        });
    }
}
