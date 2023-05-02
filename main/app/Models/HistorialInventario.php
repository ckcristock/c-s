<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;
    protected $table = 'Historial_Inventario';
    protected $primaryKey = 'Id_Historial_Inventario';
    protected $fillable = [
        'Id_Inventario_Nuevo',
        'Id_Estiba',
        'Codigo_CUM',
        'Lote',
        'Fecha_Vencimiento',
        'Cantidad',
        'Cantidad_Apartada',
        'Cantidad_Seleccionada',
        'Id_Doc_Inventario_Fisico',
        'Id_Producto',
    ];
}
