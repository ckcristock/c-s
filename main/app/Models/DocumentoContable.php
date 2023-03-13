<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoContable extends Model
{
    use HasFactory;

    protected $table = 'Documento_Contable';
    protected $primaryKey = 'Id_Documento_Contable';

    protected $fillable = [
        'Fecha_Documento',
        'Id_Empresa',
        'Codigo',
        'Beneficiario',
        'Tipo_Beneficiario',
        'Concepto',
        'Notas_Recibo',
        'Identificacion_Funcionario',
        'Estado',
        'Codigo_Qr',
        'Id_Centro_Costo',
        'Documento',
        'Egreso',
        'Forma_Pago',
        'Tipo',
        'Fecha_Registro',
        'Funcionario_Anula',
        'Fecha_Anulacion',
    ];
}
