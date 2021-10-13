<?php

namespace App\Http\Controllers;

use App\Http\Libs\Nomina\Facades\NominaPago;
use App\Models\Company;
use App\Models\Payroll;
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
    } catch (\Throwable $th) {
        //throw $th;
        return $th->getLine().' '. $th->getFile().' '.$th->getMessage();
    }
        
    }

    public function getPagoNeto($id, $fechaInicio, $fechaFin)
    {

        return NominaPago::pagoFuncionarioWithId($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }
}
