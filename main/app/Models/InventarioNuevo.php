<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioNuevo extends Model
{
    use HasFactory;
    protected $table = 'Inventario_Nuevo';
    protected $primaryKey = 'Id_Inventario_Nuevo';
    protected $fillable = [
        'Codigo',
        'Id_Estiba',
        'Id_Producto',
        'Codigo_CUM',
        'Lote',
        'Fecha_Vencimiento',
        'Fecha_Carga',
        'Identificacion_Funcionario',
        'Id_Bodega',
        'Id_Punto_Dispensacion',
        'Cantidad',
        'Lista_Ganancia',
        'Id_Dispositivo',
        'Costo',
        'Cantidad_Apartada',
        'Estiba',
        'Fila',
        'Alternativo',
        'Actualizado',
        'Cantidad_Seleccionada',
        'Cantidad_Leo',
        'Negativo',
        'Cantidad_Pendientes',
    ];
}
