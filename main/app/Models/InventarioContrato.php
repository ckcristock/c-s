<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioContrato extends Model
{
    use HasFactory;
    protected $table = 'Inventario_Contrato';
    protected $primaryKey = 'Id_Inventario_Contrato';
    protected $fillable = [
        'Id_Contrato',
        'Id_Inventario_Nuevo',
        'Id_Producto_Contrato',
        'Cantidad',
        'Cantidad_Apartada',
        'Cantidad_Seleccionada',
    ];
}
