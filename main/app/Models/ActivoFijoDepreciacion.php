<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivoFijoDepreciacion extends Model
{
    use HasFactory;
    protected $table = 'Activo_Fijo_Depreciacion';
    protected $primaryKey = 'Id_Activo_Fijo_Depreciacion';
    protected $fillable = [
        'Id_Depreciacion',
        'Id_Activo_Fijo',
        'Valor_PCGA',
        'Valor_NIIF',
    ];
}
