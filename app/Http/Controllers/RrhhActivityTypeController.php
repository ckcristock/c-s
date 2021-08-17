<?php

namespace App\Http\Controllers;

use App\Models\RrhhActivityType;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class RrhhActivityTypeController extends Controller
{
    //
    use ApiResponser;

    public function index()
    {
        return $this->success(RrhhActivityType::all());
    }

    public function store(Request $request)
    {
        try {
            RrhhActivityType::create($request->all());
            return $this->success('Actualización con éxtio');

            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(),500);
        }
    }
}
