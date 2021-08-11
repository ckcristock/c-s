<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  $this->success( Eps::all(['id as value','name as text']) );
    }
}
