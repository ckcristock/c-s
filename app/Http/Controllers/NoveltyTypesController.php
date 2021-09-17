<?php

namespace App\Http\Controllers;

use App\Models\NoveltyTypes;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class NoveltyTypesController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(NoveltyTypes::all(['name As text', 'id As value']));
    }

    public function paginate()
    {
        return $this->success(
            NoveltyTypes::when( Request()->get('novelty') , function($q, $fill)
            {
                $q->where('novelty','like','%'.$fill.'%');
            }
        )
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
            $noveltyTypes  = NoveltyTypes::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($noveltyTypes->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NoveltyTypes  $noveltytypes
     * @return \Illuminate\Http\Response
     */
    public function show(NoveltyTypes $noveltyTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NoveltyTypes  $noveltyTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(NoveltyTypes $noveltyTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NoveltyTypes  $noveltyTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NoveltyTypes $noveltyTypes)
    {
        try {
            $noveltyTypes = NoveltyTypes::find(request()->get('id'));
            $noveltyTypes->update(request()->all());
            return $this->success('Tipo de novedad actualizada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NoveltyTypes  $noveltyTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $noveltyTypes = NoveltyTypes::findOrFail($id);
            $noveltyTypes->delete();
            return $this->success('Tipo de novedad eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
