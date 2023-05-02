<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroReporteResumenRetencion extends Model
{
    use HasFactory;
    protected $table = 'Parametro_Reporte_Resumen_Retencion';
    protected $primaryKey = 'Id_Parametro_Reporte_Resumen_Retencion';
    protected $fillable = [
        'Id_Plan_Cuenta',
        'Concepto',
        'Tipo_Retencion',
        'Tipo_Valor',
        'Created_At',
    ];
}
