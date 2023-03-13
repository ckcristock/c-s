<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaDocumentoContable extends Model
{
    use HasFactory;

    protected $table = 'Cuenta_Documento_Contable';
    protected $primaryKey = 'Id_Cuenta_Documento_Contable';

    protected $fillable = [
        'Id_Documento_Contable',
        'Id_Plan_Cuenta',
        'Nit',
        'Cheque',
        'Tipo_Nit',
        'Id_Centro_Costo',
        'Documento',
        'Concepto',
        'Base',
        'Debito',
        'Credito',
        'Deb_Niif',
        'Cred_Niif',
    ];
}
