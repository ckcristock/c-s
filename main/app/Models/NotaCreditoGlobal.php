<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoGlobal extends Model
{
    use HasFactory;
    protected $table = 'Nota_Credito_Global';
    protected $primaryKey = 'Id_Nota_Credito_Global';
    protected $fillable = [
        'Tipo_Factura',
        'Id_Factura',
        'Valor_Total_Factura',
        'Id_Funcionario',
        'Id_Cliente',
        'Codigo_Factura',
        'Codigo',
        'Fecha',
        'Observaciones',
        'Cude',
        'Codigo_Qr',
        'Procesada',
    ];
}
