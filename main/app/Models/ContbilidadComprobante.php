<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContbilidadComprobante extends Model
{
    use HasFactory;
    protected $table = 'Contabilidad_Comprobante';
    protected $primaryKey = 'Id_Contabilidad_Comprobante';
    protected $fillable = [
        'Id_Plan_Cuentas',
        'Id_Comprobante',
        'Id_Factura_Comprobante',
        'Debito',
        'Credito',
        'Codigo_Cuenta',
        'Nombre_Cuenta'
    ];
}
