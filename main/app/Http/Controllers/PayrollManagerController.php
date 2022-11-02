<?php

namespace App\Http\Controllers;

use App\Models\PayrollManager;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollManagerController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(PayrollManager::all());
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
            //Validar si ya existe el área y hacer un softdelete
            $responsable = PayrollManager::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($responsable->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');

        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayrollManager  $payrollManager
     * @return \Illuminate\Http\Response
     */
    public function show(PayrollManager $payrollManager)
    {
        return 'hi show';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PayrollManager  $payrollManager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayrollManager $payrollManager)
    {
        return 'hi update';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayrollManager  $payrollManager
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayrollManager $payrollManager)
    {
        return 'hi destroy';
    }
}
