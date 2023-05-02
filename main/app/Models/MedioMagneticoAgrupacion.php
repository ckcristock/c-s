<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedioMagneticoAgrupacion extends Model
{
    use HasFactory;
    protected $table = 'Medio_Magnetico_Agrupacion';
    protected $primaryKey = 'Id_Medio_Magnetico_Agrupacion';
    protected $fillable = [
        'Id_Formato_Agrupacion_Medio_Magnetico',
        'Id_Medio_Magnetico_Especial'
    ];
}
