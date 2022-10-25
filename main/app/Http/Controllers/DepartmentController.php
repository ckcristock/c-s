<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use ApiResponser;
    //
    public function index()
    {
        return $this->success(
            Department::orderBy('name', 'DESC')->get(['name As text', 'id As value'])
        );
    }

    public function paginate()
    {
        return $this->success(
            Department::orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function show($country_id)
    {
        $departamentos = Department::where('country_id', $country_id)
                                    ->orderBy('name', 'asc')
                                    ->get(['name as text', 'id as value']);
        if (count($departamentos)===0) {
            return $this->error('Estados/Departamentos no encontrados para este país', 204);
        }else{
            return $this->success(
                $departamentos
                //Department::where('country_id', $country_id)->get()
            );
        }

    }

    public function store(Request $request)
    {
        try {
            Department::create($request->all());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }
}
