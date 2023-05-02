<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'Proveedor';
    protected $primaryKey = 'Id_Proveedor';
    protected $fillable = [
        'Tipo_Identificacion',
        'Digito_Verificacion',
        'Primer_Nombre',
        'Segundo_Nombre',
        'Primer_Apellido',
        'Segundo_Apellido',
        'Nombre',
        'Razon_Social',
        'Ciudad',
        'Direccion',
        'Telefono',
        'Celular',
        'Correo',
        'Descripcion',
        'Meses_Devolucion',
        'Id_Departamento',
        'Id_Municipio',
        'Asesor_Comercial',
        'Telefono_Asesor',
        'Email_Asesor',
        'Tipo_Retencion',
        'Tipo_Reteica',
        'Id_Plan_Cuenta_Reteica',
        'Animo_Lucro',
        'Ley_1429_2010',
        'Id_Codigo_Ciiu',
        'Id_Plan_Cuenta_Retefuente',
        'Id_Plan_Cuenta_Reteiva',
        'Contribuyente',
        'Detalle',
        'Confiable',
        'Regimen',
        'Tipo',
        'Condicion_Pago',
        'Dias_Descuento',
        'Porcentaje_Descuento',
        'Estado',
        'Rut',
        'Pais',
        'Pais_Dian',
        'Tipo_Tercero',
        'Fecha_Registro',
        'Identificacion_Funcionario',
        'Cupo'
    ];

}
