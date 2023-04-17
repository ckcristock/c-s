<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraNacional extends Model
{
    use HasFactory;

    protected $table = 'Orden_Compra_Nacional';
    protected $primaryKey = 'Id_Orden_Compra_Nacional';
    protected $fillable =
    [
        'Codigo',
        'Identificacion_Funcionario',
        'Fecha',
        'Id_Bodega',
        'Tipo_Bodega',
        'Id_Bodega_Nuevo',
        'Id_Punto_Dispensacion',
        'Id_Proveedor',
        'Observaciones',
        'Fecha_Entrega_Probable',
        'Tipo',
        'Estado',
        'Fecha_Creacion_Compra',
        'Codigo_Qr',
        'Aprobacion',
        'Id_Pre_Compra'
    ];

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }

    public function productos()
    {
        return $this->belongsToMany(Product::class, "Producto_Orden_Compra_Nacional", "Id_Orden_Compra_Nacional", "Id_Producto")
        ->withPivot('Id_Inventario','Costo','Cantidad','Iva','Total')->as('detalles')
        ->withTimestamps();
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'Identificacion_Funcionario')->fullName();
    }

    public function third()
    {
        return $this->belongsTo(ThirdParty::class, 'Id_Proveedor')->fullName();
    }

    public function store()
    {
        return $this->belongsTo(Bodegas::class, 'Id_Bodega_Nuevo');
    }
}
