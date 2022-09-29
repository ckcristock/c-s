<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Models\PayrollOvertime;
use App\Models\PayrollParafiscal;
use App\Models\PayrollRisksArl;
use App\Models\PayrollSocialSecurityCompany;
use App\Models\PayrollSocialSecurityPerson;
use App\PayrollDisabilityLeave;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollConfigController extends Controller
{
    use ApiResponser;
    //

    public function horasExtrasDatos()
    {
        return PayrollOvertime::all();
    }

    public function horasExtrasUpdate($id, Request $request) 
    {
        PayrollOvertime::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }
    public function incapacidadesDatos()
    {
        return 'En desarrollo';
        //return PayrollDisabilityLeave::all();
    }

    public function parafiscalesDatos()
    {
        return PayrollParafiscal::all();
    }

    public function riesgosArlDatos()
    {
        return PayrollRisksArl::all();
    }


    public function sSocialEmpresaDatos()
    {
        return PayrollSocialSecurityCompany::all();
    }
    public function sSocialFuncionarioDatos()
    {
        return PayrollSocialSecurityPerson::all();
    }
}
