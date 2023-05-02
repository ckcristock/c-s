<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraInternacional extends Model
{
    use HasFactory;
    protected $table = 'Orden_Compra_Internacional';
    protected $primaryKey = 'Id_Orden_Compra_Internacional';
    protected $fillable = [
        'Codigo',
        'Identificacion_Funcionario',
        'Fecha_Registro',
        'Fecha',
        'Id_Bodega',
        'Id_Bodega_Nuevo',
        'Id_Proveedor',
        'Observaciones',
        'Puerto_Destino',
        'Tasa_Dolar',
        'Tipo',
        'Estado',
        'Codigo_Qr',
        'Flete_Internacional',
        'Seguro_Internacional',
        'Tramite_Sia',
        'Flete_Nacional'
    ];
}
