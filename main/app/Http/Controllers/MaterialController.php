<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            Material::orderBy('name')
                ->when(request()->get('type'), function ($q, $fill) {
                    $q->where('type',  $fill );
                })
                ->when(request()->get('cut_water') == 1, function ($q) {
                    $q->where('cut_water',1 );
                })
                ->when(request()->get('cut_laser')== 1, function ($q) {
                    $q->where('cut_laser',  1 );
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function paginate()
    {
        return $this->success(
            Material::orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $material = Material::create($request->all());
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getLine(), $th->getFile(), 500);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Material::find($id,['id','unit','unit_price','cut_water','cut_laser','type', 'name As text', 'id As value']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json(['message' => 'Material no encontrado'], 404);
        }

        $atributos = request()->all();
        $material->update($atributos);

        return response()->json(["message" => "Se ha actualizado con éxito"], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
