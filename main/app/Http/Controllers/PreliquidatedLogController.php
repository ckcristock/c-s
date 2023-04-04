<?php

namespace App\Http\Controllers;

use App\Models\PreliquidatedLog;
use App\Models\Person;
use App\Models\WorkContract;
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
    public function index(Request $request)
    {
        try {
            $people_liq = Person::alias('p')
                ->with('onePreliquidatedLog')
                ->select(
                    'p.id',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname',
                    'p.identifier',
                    'p.image',
                    'p.updated_at',
                )
                ->when($request->person_id, function ($q, $fill) {
                    $q->where('id', $fill);
                })
                ->where('p.status', 'PreLiquidado')
                ->get()
                ->load('onePreliquidatedLog')
                ->sortByDesc('onePreliquidatedLog.liquidated_at')
                ->values()
                /* ->paginate(Request()->get('pageSize', 12), ['*'], 'page', Request()->get('page', 1)) */;

            return $this->success($people_liq);
        } catch (\Throwable $th) {
            return $this->error([
                'message' => 'Ha ocurrido un error',
                'data' => $th->getMessage() . ' msg: ' . $th->getLine() . ' ' . $th->getFile()
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
            if ($request->status == 'PreLiquidado') {
                $work_contract = WorkContract::find($request->contract_work);
                $work_contract->update([
                    'liquidated' => 1,
                    'date_end' => $request->liquidated_at,
                    'old_date_end' => $work_contract->date_end
                ]);
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
            } else if ($request->status == 'Reincorporado') {
                $log = PreliquidatedLog::where('person_id', $request->id)->latest()->first();
                $work_contract = WorkContract::find($log->person_work_contract_id);
                $work_contract->update([
                    'liquidated' => 0,
                    'date_end' => $work_contract->old_date_end,
                    'old_date_end' => null
                ]);
                $responsable = $request->reponsible;
                $nuevo = PreliquidatedLog::create([
                    "person_id" => $request->id,
                    "person_identifier" => $request->identifier,
                    "full_name" => $request->full_name,
                    "liquidated_at" => $request->liquidated_at,
                    "person_work_contract_id" => $log->person_work_contract_id,
                    "reponsible_id" => $responsable['person_id'],
                    "responsible_identifier" => $responsable['usuario'],
                    "status" => $request->status,
                ]);
            }


            return $this->success([
                'status' => 'success',
                'message' => 'Registro creado exitosamente',
                'data' => $nuevo
            ]);
        } catch (\Throwable $th) {
            return $this->error([
                'status' => 'error',
                'message' => "Ha ocurrido un error inesperado",
                'data' => $th->getMessage() . ' line: ' . $th->getLine() . ' ' . $th->getFile()
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
