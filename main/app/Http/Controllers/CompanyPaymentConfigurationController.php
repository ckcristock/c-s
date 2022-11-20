<?php

namespace App\Http\Controllers;

use App\Models\CompanyPaymentConfiguration;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CompanyPaymentConfigurationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     * Muestra los datos de la compañia 1
     * porque es una sola empresa
     * Cambiar si se requiere multiempresa
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CompanyPaymentConfiguration::where('company_id', 1)->first();
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
            $companyConfiguration = CompanyPaymentConfiguration::updateOrCreate([ 'company_id'=> $request->get('company_id') ], $request->all());
            return ($companyConfiguration->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
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
