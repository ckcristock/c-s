<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceInicialActivoFijo extends Model
{
    use HasFactory;
    protected $table = 'Balance_Inicial_Activo_Fijo';
    protected $primaryKey = 'Id_Activo_Fijo';
    protected $fillable = [
        'Fecha',
        'Vida_Util_Restante_PCGA',
        'Vida_Util_Restante_NIIF',
        'Depreciacion_Acum_PCGA',
        'Depreciacion_Acum_NIIF',
        'Saldo_PCGA',
        'Saldo_NIIF',
        'Estado'
    ];
}
