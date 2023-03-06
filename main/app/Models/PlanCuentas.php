<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanCuentas extends Model
{
    use HasFactory;

    protected $table = 'Plan_Cuentas';
    protected $primaryKey = 'Id_Plan_Cuentas';

    protected $fillable = [
        'Id_Plan_Cuentas',
        'Tipo_P',
        'Tipo_Niif',
        'Codigo',
        'Codigo_Padre',
        'Nombre',
        'Codigo_Niif',
        'Nombre_Niif',
        'Estado',
        'Ajuste_Contable',
        'Cierra_Terceros',
        'Movimiento',
        'Documento',
        'Base',
        'Valor',
        'Porcentaje',
        'Centro_Costo',
        'Depreciacion',
        'Amortizacion',
        'Exogeno',
        'Naturaleza',
        'Maneja_Nit',
        'Cie_Anual',
        'Nit_Cierre',
        'Banco',
        'Cod_Banco',
        'Nit',
        'Clase_Cta',
        'Cta_Numero',
        'Reporte',
        'Niif',
        'Porcentaje_Real',
        'Tipo_Cierre_Mensual',
        'Tipo_Cierre_Anual',
        'Id_Empresa',
    ];

    public function cuenta_padre()
    {
        return $this->hasMany(PlanCuentas::class, 'Codigo_Padre', 'Codigo_Niif')
            ->with('cuenta_padre')
            ->select('Id_Plan_Cuentas', 'Nombre_Niif', 'Codigo_Niif', 'Codigo_Padre');
    }
}
