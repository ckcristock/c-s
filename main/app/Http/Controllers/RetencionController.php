<?php

namespace App\Http\Controllers;

use App\Models\Retencion;
use Illuminate\Http\Request;
use App\Http\Services\HttpResponse;
use App\Http\Services\QueryBaseDatos;

class RetencionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $http_response = new HttpResponse();

        $query = 'SELECT R.*
			FROM Retencion R
			ORDER BY R.Nombre ASC';


        $queryObj = new QueryBaseDatos($query);
        $retenciones = $queryObj->ExecuteQuery('Multiple');

        return json_encode($retenciones);
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
     * @param  \App\Models\Retencion  $retencion
     * @return \Illuminate\Http\Response
     */
    public function show(Retencion $retencion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Retencion  $retencion
     * @return \Illuminate\Http\Response
     */
    public function edit(Retencion $retencion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Retencion  $retencion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Retencion $retencion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Retencion  $retencion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Retencion $retencion)
    {
        //
    }
}
