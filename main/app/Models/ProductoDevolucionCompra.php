<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoDevolucionCompra extends Model
{
    use HasFactory;
    protected $table = 'Producto_Devolucion_Compra';
    protected $primaryKey = 'Id_Producto_Devolucion_Compra';
    protected $fillable = [
        'Id_Producto',
        'Id_Inventario',
        'Id_Inventario_Nuevo',
        'Lote',
        'Fecha_Vencimiento',
        'Id_Devolucion_Compra',
        'Cantidad',
        'Motivo',
        'Costo',
        'Impuesto',
    ];
}
