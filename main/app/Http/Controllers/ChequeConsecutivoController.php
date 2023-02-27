<?php

namespace App\Http\Controllers;

use App\Models\ChequeConsecutivo;
use Illuminate\Http\Request;
use App\Http\Services\consulta;

class ChequeConsecutivoController extends Controller
{


    public function lista()
    {
        $query = "SELECT * FROM Cheque_Consecutivo WHERE Estado = 'Activo'";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();

        foreach ($resultado as $i => $valor) {
            $resultado[$i]->value = $valor->Prefijo . str_pad($valor->Consecutivo, 4, '0', STR_PAD_LEFT);
            $resultado[$i]->label = $valor->Prefijo . str_pad($valor->Consecutivo, 4, '0', STR_PAD_LEFT);
        }

        return json_encode($resultado);
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
     * @param  \App\Models\ChequeConsecutivo  $chequeConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function show(ChequeConsecutivo $chequeConsecutivo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChequeConsecutivo  $chequeConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function edit(ChequeConsecutivo $chequeConsecutivo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChequeConsecutivo  $chequeConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChequeConsecutivo $chequeConsecutivo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChequeConsecutivo  $chequeConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChequeConsecutivo $chequeConsecutivo)
    {
        //
    }
}
