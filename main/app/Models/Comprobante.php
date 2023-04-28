<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $table = 'Comprobante';
    protected $primaryKey = 'Id_Comprobante';
    protected $fillable = [
        'Id_Funcionario',
        'Id_Cliente',
        'Id_Proveedor',
        'Fecha_Comprobante',
        'Fecha_Registro',
        'Id_Forma_Pago',
        'Cheque',
        'Id_Cuenta',
        'Observaciones',
        'Notas',
        'Codigo',
        'Tipo',
        'Tipo_Movimiento',
        'Codigo_Qr',
        'Estado',
        'Funcionario_Anula',
        'Fecha_Anulacion',
    ];
}
