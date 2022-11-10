<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPlan extends Model
{
    use HasFactory;

    protected $table = 'plan_cuentas';

    protected $primaryKey  ='Id_Plan_Cuentas';

    protected $fillable = [
        'Tipo_P',
        'Tipo_Niif',
        'Codigo',
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
    ];

    /* protected $table = 'account_plan'; */
	public function balance()
	{
		return $this->hasOne(AccountPlanBalance::class);
	}

    public function cuentaHorasExtras()
    {
        return $this->hasMany(PayrollOvertime::class, 'account_plan_id', 'Codigo');
    }

    public function cuentaSegSocialFunc()
    {
        return $this->hasMany(PayrollSocialSecurityPerson::class, 'account_plan_id', 'Codigo');
    }

    public function cuentaSegSocialEmpresa()
    {
        return $this->hasMany(PayrollSocialSecurityCompany::class, 'account_plan_id', 'Codigo');
    }

    public function cuentaRiesgos()
    {
        return $this->hasMany(PayrollRisksArl::class, 'account_plan_id', 'Codigo');
    }

    public function cuentaParafiscales()
    {
        return $this->hasMany(PayrollParafiscal::class, 'account_plan_id', 'Codigo');
    }

    public function cuentaNovedades(){
        return $this->hasMany(DisabilityLeave::class, 'account_plan_id','Codigo');
    }

    public function cuentaIncapacidades(){
        return $this->hasMany(PayrollDisabilityLeave::class, 'accounting_account','Codigo');
    }

}
