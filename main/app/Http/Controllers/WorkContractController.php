<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\WorkContractFinishConditions;
use App\Models\Person;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use NumberFormatter;

class WorkContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 20;
        return $this->success(
            Person::alias('p')
                ->select(
                    DB::raw('concat("CON", w.id) as code'),
                    'p.id',
                    'p.identifier',
                    'p.first_name',
                    'p.first_surname',
                    'p.second_name',
                    'p.second_surname',
                    'p.image',
                    'p.status',
                    'p.created_at',
                    'posi.name as position',
                    'depe.name as dependency_name',
                    'gr.name as group_name',
                    'co.name as company_name',
                    'w.date_of_admission',
                    'w.date_end',
                    'wt.name as work_contract_type',
                    'wt.conclude'
                )
                ->when(Request()->get('person'), function ($q, $fill) {
                    $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
                })
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('work_contract_types as wt', 'wt.id', 'w.work_contract_type_id')
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as depe', function ($join) {
                    $join->on('depe.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'group_id');
                })
                ->when(Request()->get('position'), function ($q, $fill) {
                    $q->where('posi.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('dependency'), function ($q, $fill) {
                    $q->where('depe.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('group'), function ($q, $fill) {
                    $q->where('gr.id', 'like', '%' . $fill . '%');
                })
                ->join('companies as co', function ($join) {
                    $join->on('co.id', '=', 'w.company_id');
                })
                ->when(Request()->get('company'), function ($q, $fill) {
                    $q->where('co.id', 'like', '%' . $fill . '%');
                })
                ->orderByDesc('p.created_at')
                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }


    public function contractsToExpire()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 2;

        // Contamos los contratos renovados con esa persona (No incluye el original).
        $renewedContractsCount = WorkContract::select("person_id", DB::raw("count(id) as cantidad"))
            ->where("liquidated", 1)
            ->groupBy("person_id");

        $peopleRenewedContract = Person::alias('p')
            ->select(
                'p.id',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'p.image',
                'w.id as contract_id',
                'w.date_of_admission',
                'w.date_end',
                DB::raw('wc.renewed'),
                DB::raw('ifnull(rc.cantidad,0) AS cantidad'),
                DB::raw('wc.id as process_id')
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id')
                    ->where("liquidated", 0)->whereNotNull('date_end')->whereBetween('date_end', [Carbon::now(), Carbon::now()->addDays(45)]);
            })->leftJoinSub($renewedContractsCount, 'rc', function ($join) {
                $join->on('rc.person_id', '=', 'p.id');
            })->leftJoin("work_contract_finish_conditions as wc", function ($join) {
                $join->on("wc.person_id", "=", "p.id");
            });


        return $this->success(
            /* DB::table('people as p')
                ->select(
                    'p.id',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname',
                    'p.image',
                    'w.date_of_admission',
                    'w.date_end'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereNotNull('date_end')->whereBetween('date_end', [Carbon::now(), Carbon::now()->addDays(45)])->orderBy('name', 'Desc');
                }) */
            $peopleRenewedContract->paginate($pageSize, ['*'], 'page', $page)
        );
    }

    public function contractRenewal($process_id)
    {
        return $this->success(
            WorkContractFinishConditions::alias('w')
                ->select(
                    DB::raw("concat(
                    p.first_name,
                  IF(ifnull(p.second_name,'') != '',' ',''),
                    ifnull(p.second_name,''),
                    ' ',
                    p.first_surname,
                  IF(ifnull(p.second_surname,'') != '',' ',''),
                    ifnull(p.second_surname,'')
                ) as name"),
                    'c.name as company_name',
                    'w.*',
                    DB::raw("DATEDIFF(date_end, old_date_end) AS date_diff")
                )
                ->join('people as p', function ($join) {
                    $join->on('p.id', '=', 'w.person_id');
                })
                ->join('companies as c', function ($join) {
                    $join->on('c.id', '=', 'w.company_id');
                })
                ->where('w.id', '=', $process_id)->first()
        );
    }

    public function getPreliquidated()
    {
        $people = Person::alias('p')
            ->select(
                'p.id',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'p.image',
                'posi.name',
                'p.updated_at',
                'posi.name as position',
                'p.created_at'
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id');
            })
            ->join('positions as posi', function ($join) {
                $join->on('posi.id', '=', 'w.position_id');
            })
            ->where('status', 'PreLiquidado')
            ->orderByDesc('p.updated_at')
            ->paginate(Request()->get('pageSize', 12), ['*'], 'page', Request()->get('page', 1));
        /* for ($i = 0; $i < count($people); $i++) {
                $fecha = $people[$i]->updated_at;
                // dd($fecha);
                // $fechas = Carbon::parse()->locale('es')->diffForHumans();
            } */
        return $this->success($people);
    }

    public function getLiquidated($id)
    {
        return $this->success(
            Person::with('work_contract')->where('id', '=', $id)->first()
        );
    }

    public function getTrialPeriod()
    {
        $contratoIndeDefinido = Person::alias('p')
            ->select(
                'p.image',
                'w.id',
                'w.date_of_admission',
                DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
                DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 60 DAY),"%m-%d") as dates'),
                DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days'),
            )
            ->where('wt.conclude', '=', 0)
            ->where('p.status', '=', 'Activo')
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('work_contract_types as wt', function ($join) {
                $join->on('wt.id', '=', 'w.work_contract_type_id');
            })
            ->havingBetween('worked_days', [40, 60]);

        $contratoFijo = Person::alias('p')
            ->select(
                'p.image',
                'w.id',
                'w.date_of_admission',
                DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
                DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 36 DAY),"%m-%d") as dates'),
                DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days')
            )
            ->where('wt.conclude', '=', 1)
            ->where('p.status', '=', 'Activo')
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('work_contract_types as wt', function ($join) {
                $join->on('wt.id', '=', 'w.work_contract_type_id');
            })
            ->whereRaw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) <=TRUNCATE( (TIMESTAMPDIFF (DAY, w.date_of_admission, w.date_end) / 5),0)')
            ->union($contratoIndeDefinido)
            ->get();
        return $this->success(
            $contratoFijo
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    use ApiResponser;

    public function show($id)
    {
        return $this->success(
            Person::alias('p')
                ->select(
                    'p.id as person_id',
                    DB::raw("concat(
                        p.first_name,
                      IF(ifnull(p.second_name,'') != '',' ',''),
                        ifnull(p.second_name,''),
                        ' ',
                        p.first_surname,
                      IF(ifnull(p.second_surname,'') != '',' ',''),
                        ifnull(p.second_surname,'')
                    ) as name"),
                    'posi.name as position_name',
                    'd.name as dependency_name',
                    'gr.name as group_name',
                    'd.group_id',
                    'w.fixed_turn_id',
                    'w.rotating_turn_id',
                    'w.contract_term_id',
                    'w.work_contract_type_id',
                    'posi.dependency_id',
                    'f.name as fixed_turn_name',
                    'r.name as rotating_turn_name',
                    'c.name as company_name',
                    'w.turn_type',
                    'w.position_id',
                    'w.company_id',
                    DB::raw("DATEDIFF(w.date_end,w.date_of_admission) AS date_diff"),
                    DB::raw('ADDDATE(w.date_end,1) as date_of_admission'),
                    DB::raw("ADDDATE(w.date_end,DATEDIFF(w.date_end,w.date_of_admission)) AS date_end"),
                    'w.date_end as old_date_end',
                    'w.salary',
                    'w.id'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as d', function ($join) {
                    $join->on('d.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'd.group_id');
                })
                ->leftJoin('fixed_turns as f', function ($join) {
                    $join->on('f.id', '=', 'w.fixed_turn_id');
                })
                ->leftJoin('rotating_turns as r', function ($join) {
                    $join->on('r.id', '=', 'w.rotating_turn_id');
                })
                ->join('companies as c', function ($join) {
                    $join->on('c.id', '=', 'w.company_id');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function getTurnTypes()
    {
        $listaRaw = explode(",", DB::table('information_schema.COLUMNS')
            ->selectRaw("substr(left(column_type,LENGTH(column_type)-1),6) AS lista_turnos")
            ->whereRaw('CONCAT_WS("-",table_schema,TABLE_NAME,COLUMN_NAME)=?', [env('DB_DATABASE') . "-work_contracts-turn_type"])
            ->first()->lista_turnos);
        $lista = [];
        foreach ($listaRaw as $value) {
            $lista[] = ["tipoTurno" => str_replace("'", "", $value)];
        }
        return $this->success($lista);
    }

    /**
     * Acciones antes de la finalización del contrato: Reovación o preliquidación.
     */
    public function finishContract(Request $request)
    {
        $value = WorkContractFinishConditions::updateOrCreate(['id' => request()->get('id')], $request->all());
        $nombreProceso = (request()->get('renewed') == 1) ? "Renovación" : "Preliquidación";
        return $this->success(
            ($value->wasRecentlyCreated) ? $nombreProceso . ' registrada con éxito' : $nombreProceso . ' modificada con éxito'
        );
    }

    public function updateEnterpriseData(Request $request)
    {
        try {
            $work_contract = WorkContract::find($request->get('id'));
            $work_contract->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function pdf($id)
    {
        $person = Person::where('id', $id)->name()->with('contractultimate', 'work_contract')->first();
        $numberFormat = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $enLetras = $numberFormat->format($person->contractultimate->salary);
        $fechaInicio = Carbon::parse($person->contractultimate->date_of_admission)->locale('es')->translatedFormat('j F Y');
        $fechaFin = Carbon::parse($person->contractultimate->date_end)->locale('es')->translatedFormat('j F Y');
        $fechaI = new DateTime($person->contractultimate->date_of_admission);
        $fechain = date($person->contractultimate->date_of_admission);
        $fechaF = new DateTime($person->contractultimate->date_end);
        $fechaCreado = Carbon::parse($person->contractultimate->created_at)->locale('es')->translatedFormat('j F Y');
        $diferencia = $fechaI->diff($fechaF);
        $prueba = $diferencia->format("%m") * 30 / 5;
        if ($prueba > 60) {
            $prueba = 60;
        }
        $fechaFinPrueba = Carbon::parse(date("j F Y", strtotime($fechain . " + $prueba days")))->locale('es')->translatedFormat('j F Y');
        $created = new Carbon($person->contractultimate->created_at);
        $dia = $created->format('d');
        $mes = Carbon::parse($created)->locale('es')->translatedFormat('F');
        $year = Carbon::parse($created)->locale('es')->translatedFormat('Y');
        $diaenletras = $numberFormat->format($dia);
        $diferenciaLetras = $numberFormat->format($diferencia->format("%m"));
        $construct_dates = (object) array(
            'diferencia' => $diferencia,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'prueba' => $prueba,
            'fechaFinPrueba' => $fechaFinPrueba,
            'enLetras' => $enLetras,
            'diferenciaLetras' => $diferenciaLetras,
            'diaenletras' => $diaenletras,
            'dia' => $dia,
            'mes' => $mes,
            'year' => $year,
            'fechaCreado' => $fechaCreado
        );
        if ($person->contractultimate->work_contract_type->name == 'Fijo') {
            $pdf = PDF::loadView('pdf.contrato_fijo', [
                'person' => $person,
                'construct_dates' => $construct_dates
            ]);
        } else if ($person->contractultimate->work_contract_type->name == 'Indefinido') {
            $pdf = PDF::loadView('pdf.contrato_indefinido', [
                'person' => $person,
                'construct_dates' => $construct_dates
            ]);
        } else if ($person->contractultimate->work_contract_type->name == 'Obra/Labor') {
            $pdf = PDF::loadView('pdf.contrato_obra_labor', [
                'person' => $person,
                'construct_dates' => $construct_dates
            ]);
        }
        return $pdf->download('contrato.pdf');
    }
}
