<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    use HasFactory;
    protected $table = 'Nota_Credito';
    protected $primaryKey = 'Id_Nota_Credito';
    protected $fillable = [
        'Observacion',
        'Id_Factura',
        'Codigo',
        'Fecha',
        'Codigo_Qr',
        'Identificacion_Funcionario',
        'Id_Cliente',
        'Estado',
        'Motivo_Rechazo',
        'Fecha_Anulacion',
        'Funcionario_Anula',
        'Id_Bodega_Nuevo',
        'Cude',
        'Procesada'
    ];
}
