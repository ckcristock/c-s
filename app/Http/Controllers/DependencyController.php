<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    //
    use ApiResponser;
    public function index(){
      return   $this->success( Dependency::all() );
    }
    
}
