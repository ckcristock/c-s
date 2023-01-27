<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'Producto';
    protected $primaryKey = 'Id_Producto';
    protected $fillable =
    [
        'Principio_Activo',
        'Presentacion',
        'Concentracion',
        'Nombre_Comercial',
        'Embalaje',
        'Laboratorio_Generico',
        'Laboratorio_Comercial',
        'Familia',
        'Codigo_Cum',
        'Cantidad_Minima',
        'Cantidad_Maxima',
        'ATC',
        'Descripcion_ATC',
        'Invima',
        'Fecha_Expedicion_Invima',
        'Fecha_Vencimiento_Invima',
        'Precio_Minimo',
        'Precio_Maximo',
        'Tipo_Regulacion',
        'Tipo_Pos',
        'Via_Administracion',
        'Unidad_Medida',
        'Cantidad',
        'Regulado',
        'Tipo',
        'Peso_Presentacion_Minima',
        'Peso_Presentacion_Regular',
        'Peso_Presentacion_Maxima',
        'Codigo_Barras',
        'Cantidad_Presentacion',
        'Mantis',
        'Imagen',
        'Id_Categoria',
        'Nombre_Listado',
        'Referencia',
        'Gravado',
        'RotativoC',
        'RotativoD',
        'Tolerancia',
        'Actualizado',
        'Unidad_Empaque',
        'Porcentaje_Arancel',
        'Forma_Farmaceutica',
        'Estado',
        'Estado_DIAN_Covid19',
        'Id_Subcategoria',
        'Tipo_Catalogo',
        'Orden_Compra',
        'Ubicar',
        'company_id',
        'Producto_Dotation_Type_Id',
        'CantUnMinDis',
        'Id_Tipo_Activo_Fijo',
        'Orden_Compra'
    ];

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }

    public function ordenes_compra_nacionales()
    {
        return $this->belongsToMany(OrdenCompraNacional::class, "Producto_Orden_Compra_Nacional", "Id_Producto", "Id_Orden_Compra_Nacional")
        ->withPivot('Id_Inventario','Costo','Cantidad','Iva','Total')->as('detalles')
        ->withTimestamps();
    }

}



