<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorradorContabilidad extends Model
{
    use HasFactory;

    protected $table = 'Borrador_Contabilidad';
    protected $primaryKey = 'Id_Borrador_Contabilidad';

    protected $fillable = [
        'Codigo',
        'Tipo_Comprobante',
        'Identificacion_Funcionario',
        'Datos',
        'Estado',
    ];
}
