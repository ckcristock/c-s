<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ApiResponser;
    //

    public function index()
    {
        return $this->success(Group::all(['name as text', 'id as value']));
    }

    public function store(Request $request)
    {
        try {
             Group::updateOrCreate( ['id' => $request->get('id')],$request->all());
            return $this->success('Creado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }
    public function destroy($id)
    {
        Group::destroy(['id' => $id]);
        return $this->success('eliminado con exito');
    }
}
