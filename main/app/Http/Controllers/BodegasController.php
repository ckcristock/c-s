<?php

namespace App\Http\Controllers;

use App\Models\Bodegas;
use App\Models\Estiba;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class BodegasController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Bodegas::with('estibas')->get());
    }

    public function paginate()
    {
        return $this->success(Bodegas::with('estibas')->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1)));
    }

    public function activarInactivar(){
        return $this->success(
            Bodegas::where('Id_Bodega_Nuevo',request()->get('id'))
            ->update(['Estado' => request()->get('state')])
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
