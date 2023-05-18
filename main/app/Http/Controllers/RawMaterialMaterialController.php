<?php

namespace App\Http\Controllers;

use App\Models\RawMaterialMaterial;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class RawMaterialMaterialController extends Controller
{
    use ApiResponser;

    public function paginate()
    {
        return $this->success(
            RawMaterialMaterial::with('product')
                ->paginate(request()->get('pageSize', 50), ['*'], 'page', request()->get('page', 1))
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->success(
            RawMaterialMaterial::with('product')
                ->when($request->name, function ($q, $fill) {
                    $q->whereHas('product', function ($query) use ($fill) {
                        $query->where('Nombre_Comercial', 'like', "%$fill%");
                    });
                })
                /* ->when($request->limit, function ($query) {
                    return $query->limit(10);
                }) */
                ->get()
                ->transform(function ($material) {
                    $material->text = strtoupper($material->product->name);
                    unset($material->product);
                    return $material;
                })
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
        $materialDB = RawMaterialMaterial::create($request->all());
        return $this->success('Creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        RawMaterialMaterial::find($id)->update($request->all());
        return $this->success('Actualizado con éxito');
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
