<?php

namespace App\Http\Controllers;

use App\Models\ComprobanteConsecutivo;
use App\Models\Quotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ComprobanteConsecutivoController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function paginate(Request $request)
    {
        return $this->success(
            ComprobanteConsecutivo::when($request->type, function ($q, $fill) {
                $q->where('Tipo', 'like', "%$fill%");
            })
            ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
    }

    public function getConsecutive($table) {
        return $this->success(getConsecutive($table));
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
     * @param  \App\Models\ComprobanteConsecutivo  $comprobanteConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function show(ComprobanteConsecutivo $comprobanteConsecutivo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComprobanteConsecutivo  $comprobanteConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function edit(ComprobanteConsecutivo $comprobanteConsecutivo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComprobanteConsecutivo  $comprobanteConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComprobanteConsecutivo $comprobanteConsecutivo)
    {
        $comprobanteConsecutivo->update($request->all());
        return $this->success('Consecutivo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComprobanteConsecutivo  $comprobanteConsecutivo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComprobanteConsecutivo $comprobanteConsecutivo)
    {
        //
    }
}
