<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Models\Payroll;
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
       /*  $horasExtras =PayrollOvertime::all(); //Evaluar si el id relacional es igual
        foreach ($horasExtras as $horaE) {
            $cuenta = $this->consultaAPI($horaE->account_plan_id);//son varias consultas a la DB, optimizar
            if (gettype($cuenta)=="array" && !empty($cuenta)){
                $horaE->cuenta_contable = $cuenta[0];
            }
        }
        return $this->success($horasExtras); */
        return PayrollOvertime::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
    }

    public function incapacidadesDatos()
    {
        return PayrollDisabilityLeave::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
    }

    /**
     * consulta que tarda 3seg o 5,95seg (09-11-22)
     * porque consulta api de PHP, mejorar
     * dy
     * */
    public function novedadesList ()
    {
        $novedades = DisabilityLeave::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
        /* foreach ($novedades as $novedad) {
            $cuenta = $this->consultaAPI($novedad->accounting_account);//son varias consultas a la DB, optimizar
            if (gettype($cuenta)=="array" && !empty($cuenta)){
                $novedad->cuenta_contable = $cuenta[0];
            }
        } */
        return $this->success($novedades);
    }

    private function consultaAPI($coincidencia = '', $tipo = '') //tipo es pcga o nada
    {
        $direcccion = 'http://inventario.sigmaqmo.com/php/plancuentas/filtrar_cuentas.php?coincidencia='.$coincidencia.'&tipo='.$tipo.'';
        $data =  json_decode(file_get_contents($direcccion), false, 3);
        return $data;
    }

    public function parafiscalesDatos()
    {
        return PayrollParafiscal::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
    }

    public function riesgosArlDatos()
    {
        return PayrollRisksArl::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
    }

    public function sSocialEmpresaDatos()
    {
        return PayrollSocialSecurityCompany::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
    }

    public function sSocialFuncionarioDatos()
    {
        return PayrollSocialSecurityPerson::with('cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif')->get();
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
