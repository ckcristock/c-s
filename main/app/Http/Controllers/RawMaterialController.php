<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialField;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
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
            RawMaterial::with('rawMaterialFild')
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
        $rawMaterial = $request->except('fields');
        $fields = $request->get('fields');
        try {
            $rawMaterialDB = RawMaterial::create($rawMaterial);
            foreach ($fields as $field) {
                $field["raw_material_id"] = $rawMaterialDB->id;
                RawMaterialField::create($field);
            }
            return $this->success('Creado con éxtio');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
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
        $rawMaterial = $request->except('fields');
        $fields = $request->get('fields');
        try {
            RawMaterial::find($id)->update($rawMaterial);
            RawMaterialField::where("raw_material_id", $id)->delete();
            foreach ($fields as $field) {
                $field["raw_material_id"] = $id;
                RawMaterialField::create($field);
            }
            return $this->success('Creado con éxtio');
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
