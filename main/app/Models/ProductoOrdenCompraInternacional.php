<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoOrdenCompraInternacional extends Model
{
    use HasFactory;
    protected $table = 'Producto_Orden_Compra_Internacional';
    protected $primaryKey = 'Id_Producto_Orden_Compra_Internacional';
    protected $fillable = [
        'Id_Orden_Compra_Internacional',
        'Id_Producto',
        'Costo',
        'Empaque',
        'Cantidad',
        'Subtotal',
        'Cantidad_Caja',
        'Caja_Ancho',
        'Caja_Alto',
        'Caja_Largo',
        'Caja_Volumen',
    ];
}
