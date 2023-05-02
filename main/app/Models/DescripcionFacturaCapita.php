<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescripcionFacturaCapita extends Model
{
    use HasFactory;
    protected $table = 'Descripcion_Factura_Capita';
    protected $primaryKey = 'Id_Descripcion_Factura_Capita';
    protected $fillable = [
        'Id_Factura_Capita',
        'Descripcion',
        'Cantidad',
        'Precio',
        'Descuento',
        'Impuesto',
        'Total'
    ];
}
