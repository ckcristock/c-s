<?php

namespace App\Http\Controllers;

use App\Models\RrhhActivityType;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class RrhhActivityTypeController extends Controller
{
    //-
    use ApiResponser;

    public function index()
    {
        return $this->success(
            RrhhActivityType::paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }
    public function all()
    {
        return $this->success(
            RrhhActivityType::get(['name As text', 'id As value'])
        );
    }

    public function store(Request $request)
    {
        try {
            $activity = RrhhActivityType::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($activity->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(),500);
        }
    }

    public function setState(Request $request){
        try {
            $type = RrhhActivityType::findOrFail( $request->get('id') );
            $type->state = $request->get('state');
            $type->save();
            return $this->success('Actualización con éxtio');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage(),500);

        }
    }
}
