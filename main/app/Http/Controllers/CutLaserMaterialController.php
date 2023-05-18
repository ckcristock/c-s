<?php

namespace App\Http\Controllers;

use App\Models\CutLaserMaterial;
use App\Models\CutLaserMaterialValue;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CutLaserMaterialController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->success(
            CutLaserMaterial::with('cutLaserMaterialValue', 'product')
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
                $material->name = strtoupper($material->product->name);
                unset($material->product);
                return $material;
            })
        );
    }

    public function paginate()
    {
        return $this->success(
            CutLaserMaterial::with('cutLaserMaterialValue', 'product')
                /* ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                }) */
                ->paginate(request()->get('pageSize', 50), ['*'], 'page', request()->get('page', 1))
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
        $cutLaserMaterial = $request->except('materials');
        $materials = $request->get('materials');
        try {
            $materialDB = CutLaserMaterial::create($cutLaserMaterial);
            foreach ($materials as $material) {
                $material["cut_laser_material_id"] = $materialDB->id;
                CutLaserMaterialValue::create($material);
            }
            return $this->success('Creado con éxito');
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
        $cutLaserMaterials = $request->except('materials');
        $materials = $request->get('materials');
        try {
            CutLaserMaterial::find($id)->update($cutLaserMaterials);
            CutLaserMaterialValue::where("cut_laser_material_id", $id)->delete();
            foreach ($materials as $material) {
                $material["cut_laser_material_id"] = $id;
                CutLaserMaterialValue::create($material);
            }
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

}
