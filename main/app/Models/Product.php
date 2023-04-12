<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'Producto';
    protected $primaryKey = 'Id_Producto';
    protected $fillable =
    [
        'Id_Producto',
        'Presentacion',
        'Embalaje',
        'Unidad_Medida',
        'Cantidad',
        'Codigo_Barras',
        'Nombre_Comercial',
        'Imagen',
        'Id_Categoria',
        'Referencia',
        'Gravado',
        'Estado',
        'Id_Subcategoria',
        'company_id',
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

    public function unit ()
    {
        return $this->hasOne(Unit::class, 'id', 'Unidad_Medida');
    }

}



