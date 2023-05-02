<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroCertificadoIngresoRetencionRenglon extends Model
{
    use HasFactory;
    protected $table = 'Parametro_Certificado_Ingreso_Retencion_Renglon';
    protected $primaryKey = 'Id_Parametro_Certificado_Ingreso_Retencion_Renglon';
    protected $fillable = [
        'Renglon',
        'Tipo_Valor',
        'Cuentas',
        'Created_At',
    ];
}
