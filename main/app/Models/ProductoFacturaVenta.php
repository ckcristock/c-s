<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoFacturaVenta extends Model
{
    use HasFactory;

    protected $table = 'Producto_Factura_Venta';
    protected $primaryKey = 'Id_Producto_Factura_Venta';
    protected $fillable = [
        'Id_Factura_Venta',
        'Id_Inventario_Nuevo',
        'Id_Producto',
        'Lote',
        'Fecha_Vencimiento',
        'Cantidad',
        'Precio_Venta',
        'Impuesto',
        'Subtotal',
        'Id_Remision',
        'Invima',
        'producto'
    ];
}
