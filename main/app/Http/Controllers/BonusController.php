<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\BonusPerson;
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
        $data = $request->all();
        $id = $data['funcionario'];
        $responsable = Person::select(
            'id',
            'first_name',
            'second_name',
            'first_surname',
            'second_surname',
            'identifier'
            )->find($id);
            //->select('id', 'first_name', 'second_name', 'first_surname', 'second_surname', 'identifier');
            /* return ($responsable['first_name'].' '.
                                $responsable['second_name'].' '.
                                $responsable['first_surname'].' '.
                                $responsable['second_surname']); */
        $nuevo = Bonus::updateOrCreate([
            'period' => $request->period
        ], [
            'total_bonuses' => $data['total_primas'],
            'total_employees' => $data['total_empleados'],
            'payment_date' => Carbon::now(),
            'period' => $data['period'],
            'status' => $data['status'],
            'payer' => $data['funcionario'],
            'payer_identifier' => $responsable['identifier'],
            'payer_fullname' => ($responsable['first_name'].' '.
                                $responsable['second_name'].' '.
                                $responsable['first_surname'].' '.
                                $responsable['second_surname'])
        ]);

        $empleados = $data['empleados'];
        foreach ($empleados as $empleado) {
            $person_bonus = BonusPerson::updateOrCreate([
                'bonuses_id' => $nuevo->id,
                'person_id' => $empleado['id'],
            ],[
                'bonuses_id' => $nuevo->id,
                'person_id' => $empleado['id'],
                'identifier' => $empleado['identifier'],
                'fullname' => $empleado['first_name'].' '.
                              $empleado['second_name'].' '.
                              $empleado['first_surname'].' '.
                              $empleado['second_surname'],
                'worked_days' => $empleado['worked_days'],
                'amount' => $empleado['bonus'],
                'lapse' => $data['period'],
                'average_amount' => $empleado['avg_salary'],
                'payment_date' => $nuevo['payment_date']
            ]);
        }
        return ($nuevo->wasRecentlyCreated && $person_bonus->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
    }

    public function checkBonuses($period)
    {
        $bonus = Bonus::where('period', $period)->first();
        return $this->success($bonus);
    }

    public function consultaPrima(Request $request)
    {
        $data = $this->calcularPrimas($request);
        return $this->success($data);
    }

    public function calcularPrimas(Request $request)
    {
        /// cuando se seleccione el período en el front, se deben enviar estas fechas,
        //para comparar con las fechas del contrato si son mayores o menores
        $fecha_inicio = Carbon::create($request->fecha_inicio);
        $fecha_fin = Carbon::create($request->fecha_fin);

        $employees = Person::query()->with(['work_contracts' => function ($query) use ($fecha_inicio, $fecha_fin) {
            $query->select('*')
                ->whereBetween('date_end', [$fecha_inicio, $fecha_fin])
                ->orWhere('date_end', '>', $fecha_fin)
                ->orWhere('date_end', null);
        }])
            ->where('status', 'Activo')
            ->get();

        $total_primas = 0;
        $recorridos = 0;

        foreach ($employees as $employee) {
            $sum_salary = 0;
            $worked_days = 0;
            $nro_contracts = count($employee->work_contracts);

            foreach ($employee->work_contracts as $contract) {
                $admission = (is_null($contract->date_of_admission)) ? null : Carbon::create($contract->date_of_admission);
                $end = (is_null($contract->date_end)) ? null : Carbon::create($contract->date_end);
                $sum_salary += $contract->salary;

                if (!is_null($end) && ($end >= $fecha_inicio)) {  // si finalizó antes del inicio del periodo, no suma
                    //si la fecha de finalización ($end) no es null, es un contrato que ya terminó
                    //debe evaluarse si se encuentra en el periodo a calcular

                    if ($admission <= $fecha_inicio) {
                        if ($end >= $fecha_fin) {
                            $worked_days += $fecha_fin->diffInDays($fecha_inicio);
                        } elseif ($end <= $fecha_fin) {
                            $worked_days += $end->diffInDays($fecha_inicio);
                        }
                    } elseif ($admission >= $fecha_inicio) {
                        if ($end >= $fecha_fin) {
                            $worked_days += $fecha_fin->diffInDays($admission);
                        } elseif ($end <= $fecha_fin) {
                            $worked_days += $end->diffInDays($admission);
                        }
                    }
                } elseif (is_null($end)) {
                    //si es nullo, puede ser un contrato indefinido
                    if ($admission <= $fecha_inicio) {        //la fecha de admisión es antes del periodo calculado
                        $worked_days += $fecha_fin->diffInDays($fecha_inicio);
                    } elseif ($admission >= $fecha_inicio) {  //la fecha de admisión es luego del periodo calculado
                        $worked_days += $fecha_fin->diffInDays($admission);
                    }
                }
                $recorridos++;
            }

            $employee->nro_contratos = $nro_contracts;
            $employee->avg_salary = ($nro_contracts == 0) ? 0 : $sum_salary / $employee->nro_contratos;
            $employee->worked_days = ($worked_days > 180) ? 180 : $worked_days;
            $employee->bonus = ($employee->avg_salary / 360) * $employee->worked_days;
            $total_primas += $employee->bonus;
        }

        return [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'total_primas' => $total_primas,
            'total_empleados' => count($employees),
            'recorridos' => $recorridos,
            'empleados' => $employees,
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bonus  $bonus
     * @return \Illuminate\Http\Response
     */
    public function show(Bonus $bonus)
    {
        return $this->success($bonus);
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
