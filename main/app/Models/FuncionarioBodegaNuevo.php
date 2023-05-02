<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioBodegaNuevo extends Model
{
    use HasFactory;
    protected $table = 'Funcionario_Bodega_Nuevo';
    protected $primaryKey = 'Id_Funcionario_Bodega_Nuevo';
    protected $fillable = [
        'Id_Bodega_Nuevo',
        'Identificacion_Funcionario'
    ];
}
