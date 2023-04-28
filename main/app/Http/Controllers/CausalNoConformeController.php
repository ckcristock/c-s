<?php

namespace App\Http\Controllers;

use App\Models\CausalNoConforme;
use Illuminate\Http\Request;
use App\Http\Services\lista;

class CausalNoConformeController extends Controller
{

    public function listar()
    {
        $oLista = new lista('Causal_No_Conforme');
        $lista = $oLista->getlist();
        unset($oLista);

        return response()->json($lista);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\CausalNoConforme  $causalNoConforme
     * @return \Illuminate\Http\Response
     */
    public function show(CausalNoConforme $causalNoConforme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CausalNoConforme  $causalNoConforme
     * @return \Illuminate\Http\Response
     */
    public function edit(CausalNoConforme $causalNoConforme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CausalNoConforme  $causalNoConforme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CausalNoConforme $causalNoConforme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CausalNoConforme  $causalNoConforme
     * @return \Illuminate\Http\Response
     */
    public function destroy(CausalNoConforme $causalNoConforme)
    {
        //
    }
}
