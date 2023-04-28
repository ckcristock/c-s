<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaActaRecepcion extends Model
{
    use HasFactory;

    protected $table = 'Factura_Acta_Recepcion';
    protected $primaryKey = 'Id_Factura_Acta_Recepcion';
    protected $fillable = [
        'Id_Acta_Recepcion',
        'Factura',
        'Fecha_Factura',
        'Archivo_Factura',
        'Id_Orden_Compra',
        'Tipo_Compra',
        'Estado'
    ];
}
