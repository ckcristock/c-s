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
        'Embalaje_id',
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

    public function scopeAlias($q, $alias)
    {
        return $q->from($q->getQuery()->from . " as " . $alias);
    }

    public function ordenes_compra_nacionales()
    {
        return $this->belongsToMany(OrdenCompraNacional::class, "Producto_Orden_Compra_Nacional", "Id_Producto", "Id_Orden_Compra_Nacional")
            ->withPivot('Id_Inventario', 'Costo', 'Cantidad', 'Iva', 'Total')->as('detalles')
            ->withTimestamps();
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'Unidad_Medida');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'Id_Subcategoria', 'Id_Subcategoria');
    }

    public function category()
    {
        return $this->belongsTo(NewCategory::class, 'Id_Categoria', 'Id_Categoria_Nueva');
    }

    public function activity()
    {
        return $this->hasMany(ActividadProducto::class, 'Id_Producto', 'Id_Producto')->with('funcionario');
    }

    public function variables()
    {
        return $this->hasMany(VariableProduct::class, 'product_id','Id_Producto')->with('categoryVariables', 'subCategoryVariables');
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class, 'Embalaje_id');
    }
}
