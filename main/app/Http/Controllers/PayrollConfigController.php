<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Models\PayrollOvertime;
use App\Models\PayrollParafiscal;
use App\Models\PayrollRisksArl;
use App\Models\PayrollSocialSecurityCompany;
use App\Models\PayrollSocialSecurityPerson;
use App\Models\PayrollDisabilityLeave;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollConfigController extends Controller
{
    use ApiResponser;

    public function horasExtrasDatos()
    {
        return PayrollOvertime::all();
    }

    public function incapacidadesDatos()
    {
        return PayrollDisabilityLeave::all();
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

    public function horasExtrasUpdate($id, Request $request)
    {
        PayrollOvertime::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }

    public function sSocialPerson($id, Request $request)
    {
        PayrollSocialSecurityPerson::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }

    public function sSocialCompany($id, Request $request)
    {
        PayrollSocialSecurityCompany::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }

    public function riesgosArlUpdate($id, Request $request)
    {
        $updated = PayrollRisksArl ::find($id)->update($request->all());
        if ($updated) {
            return $this->success('Actualizado con éxito');
        }else{
            return $this->error('No se pudo actualizar', 400);
        }
    }

    public function parafiscalesUpdate($id, Request $request)
    {
        PayrollParafiscal::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }

    public function incapacidadesUpdate($id, Request $request)
    {
        PayrollDisabilityLeave::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
    }

}
