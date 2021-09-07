<?php

namespace App\Http\Controllers;

use App\Models\CompensationFund;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CompensationFundController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  $this->success( CompensationFund::all(['id as value','name as text']) );
    }

    public function paginate()
    {
        return $this->success(
            CompensationFund::orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function store(Request $request)
    {
        try {
            $validate =  CompensationFund::where('nit', $request->get('nit'))->first();
            if ($validate) {
                return $this->error('El nit ya está registrado', 423); 
            }
            $v = CompensationFund::where('code', $request->get('code'))->first();
            if ($v) {
                return $this->error('El código ya existe', 423);
            }
            $pensionFund = CompensationFund::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($pensionFund->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
