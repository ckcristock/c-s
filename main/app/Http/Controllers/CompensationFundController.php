<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompensationFundRequest;
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

    public function store(CompensationFundRequest $request)
    {
        try {
            $pensionFund = CompensationFund::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($pensionFund->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }
}
