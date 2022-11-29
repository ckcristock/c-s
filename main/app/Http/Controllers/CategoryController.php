<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\NewCategory;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
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
            NewCategory::select(['C.*' , 'D.Nombre as NombreDepartamento' , 'M.Nombre as NombreMunicipio'])
            ->from("Categoria_Nueva as C")
            ->join("Departments as D", function($join){
                $join->on("D.id","C.Departamento");
            })
            ->leftJoin("Municipalities as M", function($join){
                $join->on("M.id","C.Municipio");
            })->get()
        );
    }

    public function paginate()
    {
        return $this->success(
            NewCategory::select(['C.*' , 'D.name as NombreDepartamento' , 'M.name as NombreMunicipio'])
            ->from("Categoria_Nueva as C")
            ->join("Departments as D", function($join){
                $join->on("D.id","C.Departamento");
            })
            ->leftJoin("Municipalities as M", function($join){
                $join->on("M.id","C.Municipio");
            })
            ->when(request()->get("nombre"), function ($q, $fill) {
                $q->where("C.Nombre",'like','%'.$fill.'%');
            })
            ->when(request()->get("departamento"), function ($q, $fill) {
                $q->where("D.name",'like','%'.$fill.'%');
            })
            ->when(request()->get("municipio"), function ($q, $fill) {
                $q->where("M.name",'like','%'.$fill.'%');
            })
            ->when(request()->get("direccion"), function ($q, $fill) {
                $q->where("Direccion",'like','%'.$fill.'%');
            })
            ->when(request()->get("telefono"), function ($q, $fill) {
                $q->where("Telefono",'like','%'.$fill.'%');
            })
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function listCategories()
    {
        return $this->success(
            NewCategory::orderBy('Id_Categoria_Nueva', 'ASC')->get(['Nombre As text', 'Id_Categoria_Nueva As value'])
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
        //
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

