<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    use ApiResponser;
    
    public function index()
    {
        $data = Municipality::orderBy('name', 'DESC')
        ->when(Request()->get('department_id'),function($q,$param){
            $q->where('department_id',$param);
        })
        ->get(['name As text', 'id As value']);
        return $this->success($data);
    }
}
