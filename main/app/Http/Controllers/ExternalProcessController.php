<?php

namespace App\Http\Controllers;

use App\Models\ExternalProcess;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ExternalProcessController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ExternalProcess::with(
            [
            'unit' => function($q){
                $q->select('id', 'name');

            },
            ]

        )->get(['id','name','unit_cost','unit_id','name As text', 'id As value']);
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
        return ExternalProcess::with(
            [
            'unit' => function($q){
                $q->select('id', 'name');

            },
            ]

        )->find($id,['id','name','unit_cost','unit_id','name As text', 'id As value']);
    }


    public function paginate()
    {
        return $this->success(
            ExternalProcess::with('unit')
            ->orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
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
