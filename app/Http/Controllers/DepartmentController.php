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
        return $this->success(Department::orderBy('name', 'DESC')->get(['name As text', 'id As value']));
    }

}
