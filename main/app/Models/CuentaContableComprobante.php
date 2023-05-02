<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaContableComprobante extends Model
{
    use HasFactory;
    protected $table = 'Cuenta_Contable_Comprobante';
    protected $primaryKey = 'Id_Cuenta_Contable_Comprobante';
    protected $fillable = [
        'Id_Plan_Cuenta',
        'Valor',
        'Impuesto',
        'Cantidad',
        'Observaciones',
        'Subtotal',
        'Id_Comprobante'
    ];
}
