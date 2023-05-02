<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoDispensacion extends Model
{
    use HasFactory;
    protected $table = 'Punto_Dispensacion';
    protected $primaryKey = 'Id_Punto_Dispensacion';
    protected $fillable = [
        'Nombre',
        'Tipo',
        'Tipo_Entrega',
        'Departamento',
        'Municipio',
        'Direccion',
        'Telefono',
        'Responsable',
        'No_Pos',
        'Turnero',
        'Cajas',
        'Wacom',
        'Entrega_Formula',
        'Entrega_Doble',
        'Autorizacion',
        'Tipo_Dispensacion',
        'Campo_Mipres',
        'Id_Bodega_Despacho',
        'Estado'
    ];
}
