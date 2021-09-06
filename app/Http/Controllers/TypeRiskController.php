<?php

namespace App\Http\Controllers;

use App\Models\TypeRisk;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeRiskController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeRisk::get(['name As text', 'id As value']));
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
            $typeRisk  = TypeRisk::create($request->all());
            return $this->success(['message' => 'Tipo de riesgo creado correctamente', 'model' => $typeRisk]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function show(TypeRisk $typeRisk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeRisk $typeRisk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeRisk $typeRisk)
    {
        try {
            $typeRisk = TypeRisk::find(request()->get('id'));
            $typeRisk->update(request()->all());
            return $this->success('Tipo de riesgo actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeRisk = TypeRisk::findOrFail($id);
            $typeRisk->delete();
            return $this->success('Tipo de riesgo eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
