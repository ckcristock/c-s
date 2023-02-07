<?php

namespace App\Http\Controllers;

use App\Models\PreliquidatedLog;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PreliquidatedLogController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {


        $people_liq = Person::alias('p')
            ->select(
                'p.id',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'p.identifier',
                'p.image',
                'posi.name',
                'p.updated_at',
                'w.id as work_contract_id',
                'posi.name as position',
                'log.liquidated_at as log_created_at',
            )
            ->join('preliquidated_logs as log', function($join){
                $join->on('log.person_id', '=', 'p.id');
            })
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id');
            })
            ->join('positions as posi', function ($join) {
                $join->on('posi.id', '=', 'w.position_id');
            })
            ->where('p.status', 'PreLiquidado')
            ->orderByDesc('log.liquidated_at')
            ->paginate(Request()->get('pageSize', 12), ['*'], 'page', Request()->get('page', 1));

           /*  $people_liq = PreliquidatedLog::with([
                'person', 'workContract', 'user' => function ($q){
                    $q->select('person_id' ,'state');
                }
            ])
            ->paginate(Request()->get('pageSize', 12), ['*'], 'page', Request()->get('page', 1));
            */
            return $this->success($people_liq);
        }catch (\Throwable $th){
            return $this->error([
                'message' => 'Ha ocurrido un error',
                'data' =>$th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile()
            ], 204);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //return ;
            $responsable = $request->reponsible;
            $nuevo = PreliquidatedLog::create([
                "person_id" => $request->id,
                "person_identifier" => $request->identifier,
                "full_name" => $request->full_name,
                "liquidated_at" => $request->liquidated_at,
                "person_work_contract_id" => $request->contract_work,
                "reponsible_id" => $responsable['person_id'],
                "responsible_identifier" => $responsable['usuario'],
                "status" => $request->status,
            ]);

            return $this->success([
                'status' => 'success',
                'message' => 'Registro creado exitosamente',
                'data' =>$nuevo
            ]);
        }catch (\Throwable $th){
            return $this->error([
                'status' => 'error',
                'message' => "Ha ocurrido un error inesperado",
                'data' =>$th->getMessage(). ' line: ' . $th->getLine() . ' ' . $th->getFile()
            ], 204);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreliquidatedLog  $preliquidatedLog
     * @return \Illuminate\Http\Response
     */
    public function show(PreliquidatedLog $preliquidatedLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PreliquidatedLog  $preliquidatedLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PreliquidatedLog $preliquidatedLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreliquidatedLog  $preliquidatedLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreliquidatedLog $preliquidatedLog)
    {
        //
    }
}
