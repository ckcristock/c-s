<?php

namespace App\Http\Controllers;

use App\Models\TypeNovelty;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeNoveltyController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeNovelty::get(['name As text', 'id As value']));
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
            $typeNovelty  = TypeNovelty::create($request->all());
            return $this->success(['message' => 'Tipo de novedad creada correctamente', 'model' => $typeNovelty]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeNovelty  $typeNovelty
     * @return \Illuminate\Http\Response
     */
    public function show(TypeNovelty $typeNovelty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeNovelty  $typeNovelty
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeNovelty $typeNovelty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeNovelty  $typeNovelty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeNovelty $typeNovelty)
    {
        try {
            $typeNovelty = TypeNovelty::find(request()->get('id'));
            $typeNovelty->update(request()->all());
            return $this->success('Tipo de novedad actualizada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeNovelty  $typeNovelty
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeNovelty = TypeNovelty::findOrFail($id);
            $typeNovelty->delete();
            return $this->success('Tipo de novedad eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
