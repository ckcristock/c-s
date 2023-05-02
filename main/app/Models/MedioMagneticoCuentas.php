<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedioMagneticoCuentas extends Model
{
    use HasFactory;
    protected $table = 'Medio_Magnetico_Cuentas';
    protected $primaryKey = 'Id_Medio_Magnetico_Cuentas';
    protected $fillable = [
        'Id_Medio_Magnetico',
        'Id_Plan_Cuenta',
        'Concepto',
    ];
}
