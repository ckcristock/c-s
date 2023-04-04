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
        Schema::create('Producto', function (Blueprint $table) {
            $table->integer('Id_Producto', true);
            $table->string('Principio_Activo', 500)->nullable();
            $table->string('Presentacion', 500)->nullable();
            $table->string('Concentracion', 500)->nullable();
            $table->text('Nombre_Comercial')->nullable();
            $table->string('Embalaje', 500)->nullable();
            $table->string('Laboratorio_Generico', 500)->nullable();
            $table->string('Laboratorio_Comercial', 500)->nullable();
            $table->string('Familia', 500)->nullable();
            $table->string('Codigo_Cum', 255)->nullable();
            $table->integer('Cantidad_Minima')->nullable();
            $table->integer('Cantidad_Maxima')->nullable();
            $table->string('ATC', 500)->nullable();
            $table->string('Descripcion_ATC', 500)->nullable();
            $table->string('Invima', 500)->nullable();
            $table->date('Fecha_Expedicion_Invima')->nullable();
            $table->date('Fecha_Vencimiento_Invima')->nullable();
            $table->integer('Precio_Minimo')->nullable();
            $table->integer('Precio_Maximo')->nullable();
            $table->string('Tipo_Regulacion', 500)->nullable();
            $table->string('Tipo_Pos', 500)->nullable();
            $table->string('Via_Administracion', 500)->nullable();
            $table->integer('Unidad_Medida')->nullable();
            $table->decimal('Cantidad', 10, 0)->nullable();
            $table->enum('Regulado', ['Si', 'No'])->nullable();
            $table->string('Tipo', 100)->default('Medicamento');
            $table->string('Peso_Presentacion_Minima', 11)->nullable();
            $table->string('Peso_Presentacion_Regular', 11)->nullable();
            $table->string('Peso_Presentacion_Maxima', 11)->nullable();
            $table->string('Codigo_Barras', 200)->nullable()->index('Codigo_Barras_idx');
            $table->integer('Cantidad_Presentacion')->nullable()->default(1);
            $table->text('Mantis')->nullable();
            $table->text('Imagen')->nullable();
            $table->integer('Id_Categoria')->nullable();
            $table->string('Nombre_Listado', 200)->nullable();
            $table->string('Referencia', 100)->nullable();
            $table->enum('Gravado', ['Si', 'No'])->default('No');
            $table->enum('RotativoC', ['Si', 'No'])->nullable();
            $table->enum('RotativoD', ['Si', 'No'])->nullable();
            $table->integer('Tolerancia')->nullable()->default(0);
            $table->enum('Actualizado', ['Si', 'No'])->default('No');
            $table->string('Unidad_Empaque', 20)->nullable()->comment('Cantidad de unidades por caja');
            $table->string('Porcentaje_Arancel', 20)->nullable()->comment('Esto es un porcentaje representado en valor entero no dividido entre 100');
            $table->string('Forma_Farmaceutica', 250)->nullable();
            $table->enum('Estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('Estado_DIAN_Covid19', 100)->nullable();
            $table->integer('Id_Subcategoria')->nullable();
            $table->integer('CantUnMinDis')->nullable()->comment('Cantidad expresada en unidades mínimas de Dispensacion');
            $table->enum('Tipo_Catalogo', ['Medicamento', 'Material', 'Insumos', 'Activo_Fijo', 'Dotacion_EPP'])->nullable()->default('Activo_Fijo');
            $table->string('Id_Tipo_Activo_Fijo', 20)->nullable();
            $table->tinyInteger('Orden_Compra')->nullable();
            $table->tinyInteger('Ubicar')->nullable();
            $table->integer('company_id')->nullable()->default(1);
            $table->integer('Producto_Dotation_Type_Id')->nullable();
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
        Schema::dropIfExists('producto');
    }
};
