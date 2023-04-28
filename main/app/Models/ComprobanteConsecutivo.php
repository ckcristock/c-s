<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteConsecutivo extends Model
{
    use HasFactory;

    protected $table = 'Comprobante_Consecutivo';
    protected $primaryKey = 'Id_Comprobante_Consecutivo';
    protected $fillable = [
        'Tipo',
        'Prefijo',
        'Anio',
        'Mes',
        'Dia',
        'city',
        'longitud',
        'format_code',
        'Consecutivo',
        'table_name',
        'company_id',
        'editable'
    ];
}
