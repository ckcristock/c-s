<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoAgrupacionMedioMagnetico extends Model
{
    use HasFactory;
    protected $table = 'Formato_Agrupacion_Medio_Magnetico';
    protected $primaryKey = 'Id_Formato_Agrupacion_Medio_Magnetico';
    protected $fillable = [
        'Codigo_Formato',
        'Nombre_Formato',
        'Created_At',
    ];
}
