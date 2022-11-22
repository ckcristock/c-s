<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Person;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Bonus::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->fecha_inicio);
        $employees = Person::with('work_contracts')
                            ->select('id',
                                    'first_name',
                                    'second_name',
                                    'first_surname',
                                    'second_surname',
                                    'image')
                            ->where('status', 'Activo')
                            ->get();

        /// cuando se seleccione el período en el front, se deben enviar estas fechas,
        //para comparar con las fechas del contrato si son mayores o menores
        $fecha_inicio = Carbon::create($request->fecha_inicio);
        $fecha_fin = Carbon::create($request->fecha_fin);
        //$employee = Person::where('id', 11622)->with('work_contracts')->first();
        $total_primas = 0;

        foreach ($employees as $employee) {
            //$nro_contracts = count($employee->work_contracts);
            $sum_salary = 0;
            $worked_days = 0;
            $nro_contracts = 0;

            foreach ($employee->work_contracts as $contract) {
                $admission = (is_null($contract->date_of_admission)) ? null : Carbon::create($contract->date_of_admission);
                $end = (is_null($contract->date_end)) ? null : Carbon::create($contract->date_end);
                $sum_salary += $contract->salary;
                $nro_contracts++;

                if (!is_null($end) && ($end>=$fecha_inicio) ) {  // si finalizó antes del inicio del periodo, no suma
                    //si la fecha de finalización ($end) no es null, es un contrato que ya terminó
                    //debe evaluarse si se encuentra en el periodo a calcular

                    if ($admission <= $fecha_inicio) {
                        if ($end>=$fecha_fin) {
                            $worked_days += $fecha_fin->diffInDays($fecha_inicio);
                        } elseif ($end<=$fecha_fin)  {
                            $worked_days += $end->diffInDays($fecha_inicio);
                        }
                    } elseif ($admission >= $fecha_inicio) {
                        if ($end>=$fecha_fin) {
                            $worked_days += $fecha_fin->diffInDays($admission);
                        } elseif ($end<=$fecha_fin) {
                            $worked_days += $end->diffInDays($admission);
                        }
                    }

                } elseif (is_null($end)) {
                    //$nro_contracts++;
                    //si es nullo, puede ser un contrato indefinido
                    if ($admission <= $fecha_inicio) {        //la fecha de admisión es antes del periodo calculado
                        $worked_days += $fecha_fin->diffInDays($fecha_inicio);
                    } elseif ($admission >= $fecha_inicio) {  //la fecha de admisión es luego del periodo calculado
                        $worked_days += $fecha_fin->diffInDays($admission);
                    }
                }


            }
            //dd($sum_salary);
            $employee->nro_contratos = ($nro_contracts==0) ? 1 : $nro_contracts;
            $employee->avg_salary = $sum_salary/$employee->nro_contratos;
            $employee->worked_days = ($worked_days >180) ? 180 : $worked_days;
            $employee->bonus = ($employee->avg_salary /360) * $employee->worked_days;
            $total_primas += $employee->bonus;
        }


        //return $employees;
        return $this->success([
            'fecha_inicio'=>$fecha_inicio,
            'fecha_fin'=>$fecha_fin,
            'total_primas'=>$total_primas,
            'total_empleados'=>count($employees),
            'empleados'=>$employees ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bonus  $bonus
     * @return \Illuminate\Http\Response
     */
    public function show(Bonus $bonus)
    {
        return ('controlador show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bonus  $bonus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bonus $bonus)
    {
        return ('controlador update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bonus  $bonus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bonus $bonus)
    {
        return ('controlador delete');
    }
}
