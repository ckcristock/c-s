<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    protected $table = 'Factura';
    protected $primaryKey = 'Id_Factura';

    protected $fillable = [
        'Fecha_Documento',
        'Id_Bodega',
        'Id_Cliente',
        'Id_Funcionario',
        'Id_Lista_Ganancia',
        'Observacion_Factura',
        'Codigo',
        'Estado_Factura',
        'Id_Dispensacion',
        'Condicion_Pago',
        'Cuota',
        'Fecha_Pago',
        'Tipo',
        'Id_Factura_Asociada',
        'Codigo_Qr',
        'Id_Causal_Anulacion',
        'Funcionario_Anula',
        'Estado_Radicacion',
        'Id_Resolucion',
        'Actualizado',
        'Cufe',
        'ZipKey',
        'ZipBase64Bytes',
        'Procesada',
        'Nota_Credito',
        'Valor_Nota_Credito',
        'Fecha_Anulacion',
        'Funcionario_Nota',
        'Fecha_Nota',
        'Id_Dispensacion2',
    ];
}
