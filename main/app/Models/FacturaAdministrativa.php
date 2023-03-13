<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaAdministrativa extends Model
{
    use HasFactory;
    protected $table = 'Factura_Administrativa';
    protected $primaryKey = 'Id_Factura_Administrativa';

    protected $fillable = [
        'Activos_Fijos',
        'Id_Cliente',
        'Tipo_Cliente',
        'Id_Resolucion',
        'Fecha',
        'Fecha_Documento',
        'Codigo',
        'Id_Centro_Costo',
        'Identificacion_Funcionario',
        'Observaciones',
        'Nota_Credito',
        'Valor_Nota_Credito',
        'Fecha_Nota',
        'Funcionario_Nota',
        'Estado_Factura',
        'Codigo_Qr',
        'Cufe',
    ];
}
