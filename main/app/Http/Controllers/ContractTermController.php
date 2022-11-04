<?php

namespace App\Http\Controllers;

use App\Models\ContractTerm;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ContractTermController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(ContractTerm::all('name as text', 'id as value'));
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
			$nuevo  = ContractTerm::updateOrCreate(['id' => $request->get('id'), "status"=>"activo"], $request->all());
			return ($nuevo->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
		} catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getCode());
			//return response()->json([$th->getMessage(), $th->getLine()]);
		}
    }

    /**
     *
     * Retorna los elementos de CotractTerm paginado y filtrado por nombre
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate()
    {
        $data = Request()->all();
        return $this->success(
            ContractTerm::when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1)));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContractTerm  $contractTerm
     * @return \Illuminate\Http\Response
     */
    public function show(ContractTerm $contractTerm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContractTerm  $contractTerm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContractTerm $contractTerm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContractTerm  $contractTerm
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractTerm $contractTerm)
    {
        //
    }
}
