<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialField;
use App\Models\MaterialThickness;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\ProductService;



class MaterialController extends Controller
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
            Material::with('materialThickness', 'product')
                ->when($request->name, function ($q, $fill) {
                    $q->whereHas('product', function ($query) use ($fill) {
                        $query->where('Nombre_Comercial', 'like', "%$fill%");
                    });
                })
                ->when($request->limit, function ($query) {
                    return $query->limit(10);
                })
                ->get()
                ->transform(function ($material) {
                    $material->text = strtoupper($material->product->name);
                    unset($material->product);
                    return $material;
                })
        );
    }

    public function paginate()
    {
        return $this->success(
            Material::with('materialThickness', 'product')
                /* ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                }) */
                ->paginate(request()->get('pageSize', 50), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function getMaterialThickness()
    {
        return $this->success(MaterialThickness::all());
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

        $data = $request->all();

        /* $product = ProductService::saveProduct($data);


        $material = $request->except('fields');
        $material['product_id'] = $product->id;

        $fields = $request->get('fields'); */
        $thicknesses = $request->get('thicknesses');
        try {
            /* $materialDB = Material::create($material); */
            $material = Material::create($data);
            foreach ($thicknesses as $thick) {
                MaterialThickness::create([
                    'thickness_id' => $thick['thickness_id'],
                    'value' => $thick['value'],
                    'material_id' => $material->id
                ]);
                /* $field["material_id"] = $materialDB->id;
                MaterialField::create($field); */
            }

            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getLine(), $th->getFile(), 500);
        }
        /* try {
            $material = Material::create($request->all());
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getLine(), $th->getFile(), 500);

        } */
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Material::find($id, ['id', 'unit', 'unit_price', 'cut_water', 'cut_laser', 'type', 'name As text', 'id As value']);
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
        $data = $request->all();


        $material = $request->except('fields');
        $fields = $request->get('fields');
        $thicknesses = $request->get('thicknesses');

        $dynamic = request()->get("dynamic");
        //ProductService::updateProduct($data, $dynamic);


        if (!$material) {
            return response()->json(['message' => 'Material no encontrado'], 404);
        }
        try {
            Material::find($id)->update($material);
            /* MaterialField::where("material_id", $id)->delete(); */
            MaterialThickness::where("material_id", $id)->delete();
            /* foreach ($fields as $field) {
                $field["material_id"] = $id;
                MaterialField::create($field);
            } */
            foreach ($thicknesses as $thickness) {
                $thickness["material_id"] = $id;
                MaterialThickness::create($thickness);
            }
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
