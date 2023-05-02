<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilFuncionario extends Model
{
    use HasFactory;
    protected $table = 'Perfil_Funcionario';
    protected $primaryKey = 'Id_Perfil_Funcionario';
    protected $fillable = [
        'Id_Perfil',
        'Identificacion_Funcionario',
        'Titulo_Modulo',
        'Modulo',
        'Crear',
        'Editar',
        'Eliminar',
        'Ver'
    ];
}
