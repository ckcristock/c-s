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
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 10;
        return $this->success(
            Department::orderBy('name')
            ->when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->paginate($pageSize, ['*'],'page', $page)
        );
    }

    public function store( Request $request )
    {
        try {
            Department::create($request->all());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }

}
