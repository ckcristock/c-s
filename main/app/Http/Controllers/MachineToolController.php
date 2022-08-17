<?php

namespace App\Http\Controllers;

use App\Models\ExternalProcess;
use App\Models\InternalProcess;
use App\Models\MachineTool;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineToolController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            MachineTool::with(
            [
            'unit' => function($q){
                $q->select('id', 'name');

            },
            ]

        )->get(['id','name','unit_cost','unit_id','name As text', 'id As value'])
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
        try {
            if ($request->type_id == '1') {
                $unit = MachineTool::updateOrCreate(['id' => $request->get('id')], $request->all());
                return ($unit->wasRecentlyCreated)
                ?
                $this->success([
                'title' => '¡Creado con éxito!',
                'text' => 'La máquina ha sido creada satisfactoriamente'
                ])
                :
                $this->success([
                'title' => '¡Actualizado con éxito!',
                'text' => 'La maquina ha sido actualizada satisfactoriamente'
                ]);
            } else if ($request->type_id == '2') {
                $unit = InternalProcess::updateOrCreate(['id' => $request->get('id')], $request->all());
                return ($unit->wasRecentlyCreated)
                ?
                $this->success([
                'title' => '¡Creado con éxito!',
                'text' => 'El proceso ha sido creado satisfactoriamente'
                ])
                :
                $this->success([
                'title' => '¡Actualizado con éxito!',
                'text' => 'El proceso ha sido actualizado satisfactoriamente'
                ]); 
            } else if ($request->type_id == '3') {
                $unit = ExternalProcess::updateOrCreate(['id' => $request->get('id')], $request->all());
                return ($unit->wasRecentlyCreated)
                ?
                $this->success([
                'title' => '¡Creado con éxito!',
                'text' => 'El proceso ha sido creado satisfactoriamente'
                ])
                :
                $this->success([
                'title' => '¡Actualizado con éxito!',
                'text' => 'El proceso ha sido actualizado satisfactoriamente'
                ]);
            }
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return MachineTool::with(
            [
            'unit' => function($q){
                $q->select('id', 'name');

            },
            ]

        )->find($id,['id','name','unit_cost','unit_id','name As text', 'id As value']);
    }

    public function paginate()
    {
        $machine = DB::table('machine_tools as m')
        ->select(
            'm.id',
            'm.name',
            'm.unit_cost',
            'm.created_at',
            DB::raw('"Maquinas Herramientas" as type'),
            DB::raw('"1" as type_id'),
            'u.name as unit_name',
            'u.id as unit_id'
        )
        ->join('units as u', 'u.id', '=', 'm.unit_id');
        $internalProcesses = DB::table('internal_processes as i')
        ->select(
            'i.id',
            'i.name',
            'i.unit_cost',
            'i.created_at',
            DB::raw('"Procesos Internos" as type'),
            DB::raw('"2" as type_id'),
            'u.name as unit_name',
            'u.id as unit_id'
            )
            ->join('units as u', 'u.id', '=', 'i.unit_id');
        $externalProcesses = DB::table('external_processes as e')
        ->select(
            'e.id',
            'e.name',
            'e.unit_cost',
            'e.created_at',
            DB::raw('"Procesos Externos" as type'),
            DB::raw('"3" as type_id'),
            'u.name as unit_name',
            'u.id as unit_id'
        )
        ->join('units as u', 'u.id', '=', 'e.unit_id')
        ->union($internalProcesses)
        ->union($machine)
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
        return $this->success($externalProcesses);
        /* return $this->success(
            MachineTool::with('unit')
            ->orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        ); */
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
}
