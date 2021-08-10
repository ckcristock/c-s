<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    //
    use ApiResponser;

    
    public function index(Request $request){
      return   $this->success(
        Dependency::when($request->get('company_id'),function($q,$p){
          $q->where('company_id',$p);
         })
        ->get(['id as value','name as text']) );
    }
    
}
