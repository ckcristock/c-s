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
            NewCategory::with("subcategory")->active()->get()
        );
    }

    public function paginate()
    {
        return $this->success(
            NewCategory::with("subcategory")
            ->when(request()->get("nombre"), function ($q, $fill) {
                $q->where("Nombre",'like','%'.$fill.'%');
            })
            ->when(request()->get("compraInternacional"), function ($q, $fill) {
                $q->where("Compra_Internacional","=",$fill);
            })
            ->when(request()->get("separacionCategorias"), function ($q, $fill) {
                $q->where("Aplica_Separacion_Categorias","=",$fill);
            })
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function listCategories()
    {
        return $this->success(
            NewCategory::orderBy('Id_Categoria_Nueva', 'ASC')->active()->get(['Nombre As text', 'Id_Categoria_Nueva As value'])
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
            $value = NewCategory::updateOrCreate( [ 'Id_Categoria_Nueva'=> request()->get('Id_Categoria_Nueva') ] , [
                'Nombre'=> request()->get('Nombre'),
                'Compra_Internacional'=> request()->get('compraInternacional'),
                'Aplica_Separacion_Categorias'=> request()->get('separacionCategorias')
            ] );
           /*  $id=($value->wasRecentlyCreated)?$value->Id_Categoria_Nueva:request()->get('Id_Categoria_Nueva');

            $category=NewCategory::find($id);
            $category->subcategories()->sync(request()->get("Subcategorias")); */
            return ($value->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( $th->getFile()." - ".$th->getMessage() );
        }
    }

    public function turningOnOff($id,Request $request){
        try{
            $category=NewCategory::find($id);
            $category->Activo = $request->activo;
            $category->save();
            $category->subcategory()->update(['Activo' => $request->activo]);
            return  $this->success('Categoría '.(($request->activo == 0)?'anulada':'reactivada').' con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( $th->getFile()." - ".$th->getMessage() );
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

