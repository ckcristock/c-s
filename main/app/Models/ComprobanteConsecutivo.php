<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteConsecutivo extends Model
{
    use HasFactory;

    protected $table = 'Comprobante_Consecutivo';

    protected $fillable = [
        'Tipo',
        'Prefijo',
        'Consecutivo',
        'Anio',
        'Mes',
        'Dia',
        'company_id',
        'city',
        'longitud',
        'format_code'
    ];
}
