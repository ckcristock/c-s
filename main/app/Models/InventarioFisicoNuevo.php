<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioFisicoNuevo extends Model
{
    use HasFactory;
    protected $table = 'Inventario_Fisico_Nuevo';
    protected $primaryKey = 'Id_Inventario_Fisico_Nuevo';
    protected $fillable = [
        'Funcionario_Autoriza',
        'Id_Bodega_Nuevo',
        'Id_Grupo_Estiba',
        'Fecha',
    ];
}
