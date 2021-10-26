<?php

namespace App\Http\Controllers;

use App\Http\Libs\Nomina\Facades\NominaDeducciones;
use App\Http\Libs\Nomina\Facades\NominaIngresos;
use App\Http\Libs\Nomina\Facades\NominaNovedades;
use App\Http\Libs\Nomina\Facades\NominaPago;
use App\Http\Libs\Nomina\Facades\NominaProvisiones;
use App\Http\Libs\Nomina\Facades\NominaRetenciones;
use App\Http\Libs\Nomina\Facades\NominaSeguridad;
use App\Models\Company;
use App\Models\Payroll;
use App\Models\PayrollOvertime;
use App\Models\PayrollPayment;
use App\Models\Person;
use App\Services\PayrollService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    //

    use ApiResponser;


    function nextMonths()
    {
        try {
            return $this->success(PayrollService::getQuincena());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage() . $th->getLine(), 402);
        }
    }
    /**
     * Retorna el resumen del pago de nómina en el mes actual, si ya existe en la BD entonces se modifica la  respuesta agregando propiedades a la respuesta
     *
     * @return Json
     */
    public function getPayrollPay($inicio = null, $fin = null)
    {

        $frecuenciaPago =  Company::get(['payment_method'])->first()['payment_method'];
        $pagoNomina = $nomina = $paga = $idNominaExistente = null;


        $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
        $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");

        $totalSalarios = 0;
        $totalRetenciones = 0;
        $totalSeguridadSocial = 0;
        $totalParafiscales = 0;
        $totalProvisiones = 0;
        $totalExtras = 0;
        $totalIngresos = 0;
        $totalCostoEmpresa = 0;

        if ($frecuenciaPago === 'Quincenal') {
            if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                $fechaInicioPeriodo = date("Y-m-16 00:00:00");
            } else {
                $fechaFinPeriodo = date("Y-m-15 23:59:59");
            }
        }

        $fechaInicioPeriodo = $fechaInicioPeriodo;
        $fechaFinPeriodo = $fechaFinPeriodo;


        /**
         * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
         * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
         * desde el componente de historial de pagos en Vue cuando se realiza una petición.
         */
        if ($inicio) {
            $fechaInicioPeriodo = $inicio;
        }

        if ($fin) {
            $fechaFinPeriodo = $fin;
        }

        /**
         * Comprobar si ya existe un pago de nómina en el periodo
         */

        $nomina = PayrollPayment::latest()->first();

        if ($nomina) {
            $idNominaExistente = $nomina->id;
            $paga = Carbon::now()->between($nomina->inicio_periodo, $nomina->fin_periodo);
        }

        $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
        };

        $funcionarios = Person::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end');
        })->with(['payroll_factors' => $fechasNovedades])->get(['id', 'identifier', 'first_name', 'first_surname', 'image']);

        try {
            //code...
           
            foreach ($funcionarios as $funcionario) {
              
                $tempSeguridad = $this->getSeguridad($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);

                $seguridad = $tempSeguridad['valor_total_seguridad'];
                $parafiscal = $tempSeguridad['valor_total_parafiscales'];

                $tempExtras = $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);
                $extras = $tempExtras['valor_total'];

                $salario = $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'];

                $retencion = $this->getRetenciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
                $provision = $this->getProvisiones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];


                $temIngresos = $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo);


                $totalSalarios +=  $salario;
                $totalRetenciones += $retencion;
                $totalSeguridadSocial += $seguridad;
                $totalParafiscales += $parafiscal;
                $totalProvisiones += $provision;
                $totalExtras += $extras;
                $totalIngresos += $temIngresos['valor_total'];

                $funcionariosResponse[] = [
                    'id' => $funcionario->id,
                    'identidad' => $funcionario->identidad,
                    'nombres' => $funcionario->nombres,
                    'apellidos' => $funcionario->apellidos,
                    'image' => $funcionario->image,
                    'salario_neto' => $salario,
                    'novedades' => [],
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $tempExtras['horas_reportadas'],
                    'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],
                    'valor_ingresos_salariales' => ($temIngresos['valor_constitutivos'] +
                        $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                        $extras),
                    'valor_ingresos_no_salariales' => $temIngresos['valor_no_constitutivos'],
                    'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
                ];
            }



            $totalCostoEmpresa += $totalSalarios + $totalRetenciones +   $totalSeguridadSocial + $totalParafiscales + $totalProvisiones;


            return response()->json([
                'frecuencia_pago' => $frecuenciaPago,
                'inicio_periodo' => $fechaInicioPeriodo,
                'fin_periodo' => $fechaFinPeriodo,
                'salarios' =>  $totalSalarios,
                'seguridad_social' => $totalSeguridadSocial,
                'parafiscales' => $totalParafiscales,
                'provisiones' => $totalProvisiones,
                'extras' => $totalExtras,
                'ingresos' => $totalIngresos,
                'retenciones' => $totalRetenciones,
                'costo_total_empresa' => $totalCostoEmpresa,
                'nomina_paga' => $paga,
                'nomina_paga_id' => $idNominaExistente,
                'funcionarios' => $funcionariosResponse
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return [ $th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
            
        }
    }

    public function payPeople($inicio = null, $fin = null)
    {
        try {

            $frecuenciaPago =  Company::get(['payment_frequency'])->first()['payment_frequency'];

            $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
            $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");
            $funcionariosResponse = [];

            if ($frecuenciaPago === 'Quincenal') {
                if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                    $fechaInicioPeriodo = date("Y-m-16 00:00:00");
                } else {
                    $fechaFinPeriodo =     date("Y-m-15 23:59:59");;
                }
            }

            $fechaInicioPeriodo = $fechaInicioPeriodo;
            $fechaFinPeriodo = $fechaFinPeriodo;

            /**
             * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
             * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
             * desde el componente de historial de pagos en Vue cuando se realiza una petición.
             */
            if ($inicio) {
                $fechaInicioPeriodo = $inicio;
            }

            if ($fin) {
                $fechaFinPeriodo = $fin;
            }
            $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
            };
           
            $funcionarios = Person::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end');
            })->with(['payroll_factors' => $fechasNovedades])->get();
            foreach ($funcionarios as $funcionario) {

                $funcionariosResponse[] = response()->json([
                    'id' => $funcionario->id,
                    'identifier' => $funcionario->identifier,
                    'first_name' => $funcionario->first_name,
                    'first_surname' => $funcionario->first_surname,
                    'image' => $funcionario->image,
                    'salario_neto' => $this->getPagoNeto($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'],
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['horas_reportadas'],
                    'novedades' => $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],

                    'valor_ingresos_salariales' => ($this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_constitutivos'] +
                        $this->getNovedades($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                        $this->getExtrasTotales($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']),
                    'valor_ingresos_no_salariales' => $this->getIngresos($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_no_constitutivos'],
                    'valor_deducciones' => $this->getDeducciones($funcionario->id, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
                ]);
            }
            return $funcionariosResponse;
        } catch (\Throwable $th) {
     
            return [ $th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
        }
    }

    public function getPagoNeto($id, $fechaInicio, $fechaFin)
    {
        return NominaPago::pagoFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    /**
     * Calcular la cantidad y total de horas extras y recargos acumulados del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getExtrasTotales($id, $fechaInicio, $fechaFin)
    {

        return PayrollOvertime::extrasFuncionarioWithId($id)->fromTo($fechaInicio, $fechaFin);
    }

    /**
     * Calcular la cantidad y total de novedades del funcionario en el periodo
     *
     * @param int $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getNovedades($id, $fechaInicio, $fechaFin)
    {
        return NominaNovedades::novedadesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }


    public function getIngresos($id, $fechaInicio, $fechaFin)
    {

        return NominaIngresos::ingresosFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getDeducciones($id, $fechaInicio, $fechaFin)
    {

        return NominaDeducciones::deduccionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSeguridad($id, $fechaInicio, $fechaFin)
    {

        return NominaSeguridad::seguridadFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getRetenciones($id, $fechaInicio, $fechaFin)
    {
        return  NominaRetenciones::retencionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getProvisiones($id, $fechaInicio, $fechaFin)
    {
        return NominaProvisiones::provisionesFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }
}
