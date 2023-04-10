<?php

namespace App\Http\Controllers;

use App\Models\ActividadProducto;
use App\Models\CategoryVariable;
use App\Models\InventaryDotation;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Product;
use App\Models\SubcategoryVariable;
use App\Models\Unit;
use App\Models\VariableProduct;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        //$tipoCatalogo = Request()->get('tipo');

        $data = Product::alias('p')->join('Subcategoria as s', 's.id_subcategoria', 'p.id_subcategoria')
            ->join('Categoria_Nueva as c', 'c.Id_Categoria_Nueva', 's.Id_Categoria_Nueva')
            ->leftJoin('product_dotation_types as pdt', 'pdt.id', 'p.Producto_Dotation_Type_Id')
            ->leftJoin('inventary_dotations as ido', 'ido.product_id', 'p.Id_Producto')
            ->leftJoin('units as u', 'u.id', 'p.Unidad_Medida')
            ->select(
                'p.Id_Producto',
                'p.Codigo_Cum',
                'p.Codigo_Cum as Cum',
                'p.Principio_Activo',
                'p.Descripcion_ATC',
                'p.Codigo_Barras',
                'p.Presentacion',
                'p.Unidad_Medida',
                'p.Id_Categoria',
                'p.Id_Subcategoria',
                'p.Laboratorio_Generico as Generico',
                'p.Laboratorio_Comercial as Comercial',
                'p.Invima as Invima',
                'p.Imagen as Foto',
                'p.Producto_Dotation_Type_Id',
                'p.Nombre_Comercial',
                'p.Embalaje',
                'p.Tipo as Tipo',
                'p.Id_Tipo_Activo_Fijo',
                'ido.status',
                'ido.id as id_inventary_dotations',
                'ido.code as code_inventary_dotations',
                'p.Estado',
                'p.Referencia'
            );


        /*  if ($tipoCatalogo == 'Medicamento' || $tipoCatalogo == 'Material' ) { */
        # code...
        $data->selectRaw(
            'LTRIM(CONCAT(
                ifnull(p.Principio_Activo,""), if(isnull(p.Presentacion),""," "),
                ifnull(p.Presentacion,""), if(isnull(p.Concentracion),""," "),
                ifnull(p.Concentracion,""), if(isnull(p.Nombre_Comercial),""," "),
                ifnull(p.Nombre_Comercial,""), if(isnull(p.Unidad_Medida),""," "),
                ifnull(p.Unidad_Medida,""), if(isnull(p.Embalaje),""," "),
                ifnull(p.Embalaje,"")
            )) as Nombre,
            s.Nombre as Subcategoria,
            c.Nombre as Categoria,
            concat(u.name," (",u.unit,")") as Nom_unidad_medida'
        );
        /*    } */

        $data->when(request()->get("company_id"), function ($q, $fill) {
            $q->where("p.company_id", $fill);
        })
        ->when(request()->get("categoria"), function ($q, $fill) {
            $q->where("p.Id_Categoria", '=', $fill);
        })
        ->when(request()->get("subcategoria"), function ($q, $fill) {
            $q->where("p.Id_Subcategoria", '=', $fill);
        })
        ->when(request()->get("nombre"), function ($q, $fill) {
            $q->where("p.Nombre_Comercial", 'like', "%$fill%");
        })
        ->when(request()->get("estado"), function ($q, $fill) {
            $q->where("p.Estado", '=', $fill);
        })
        ->when(request()->get("imagen"), function ($q, $fill) {
            $q->where('p.Imagen',(($fill == 'con')?"!=":"="),null);
        });

        return $this->success((request()->get("id")!==null)?
            $data->where("p.Id_Producto", request()->get("id"))->first():
            $data->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1)));
    }

    public function getActividad()
    {
        return $this->success(
            ActividadProducto::alias("ar")->with(["funcionario" => function($q){
                $q->select("id","image")->completeName();
            }])
            ->where("Id_Producto",request()->get('id'))->orderBy('Fecha', 'desc')->get()
        );
    }

    public function getMaterials()
    {
        /** Funcion que obtiene los productos de la categoría materia prima y subcategoria materiales para llamar
         * en materiales comerciales de apu y para mostrar en la parametrización de apu materiales corte agua y corte láser
         */
        $product = Product::where('Id_Categoria', 1)->where('Id_Subcategoria', 1)->get(['Id_Producto as id', 'Id_Producto as value', 'Nombre_Comercial as text']);
        return $this->success($product);
    }

    public function getVars()
    {

        $producto = request()->get('producto');
        $query = [];
        if ($producto !== null) {
            $query["cat"] = VariableProduct::alias("vp")
                ->select("vp.id as vp_id", "cv.id as cv_id", "label", "type", "required", "valor")
                ->join('category_variables as cv', 'cv.id', 'vp.category_variables_id')
                ->where("vp.product_id", $producto)->get();

            if (count($query["cat"]) == 0) {
                $query["cat"] = CategoryVariable::select("category_variables.id as cv_id", "label", "type", "required")
                    ->join('Producto as p', 'p.Id_Categoria', "category_variables.category_Id")
                    ->where("Id_Producto", $producto)->get();
            }

            $query["subcat"] = VariableProduct::alias("vp")
                ->select("vp.id as vp_id", "sv.id as sv_id", "label", "type", "required", "valor")
                ->join('subcategory_variables as sv', 'sv.id', 'vp.subcategory_variables_id')
                ->where("vp.product_id", $producto)->get();

            if (count($query["subcat"]) == 0) {
                $query["subcat"] = SubcategoryVariable::select("subcategory_variables.id as sv_id", "label", "type", "required")
                    ->join('Producto as p', 'p.Id_Subcategoria', 'subcategory_variables.Subcategory_Id')
                    ->where("Id_Producto", $producto)->get();
            }/*  */
        } else {
            $query["cat"] = CategoryVariable::select("id as cv_id", "label", "type", "required")
                ->where("Category_Id", request()->get('categoria'))->get();

            $query["subcat"] = SubcategoryVariable::select("id as sv_id", "label", "type", "required")
                ->where("Subcategory_Id", request()->get('subcategoria'))->get();
        }
        return $this->success($query);
    }


    public function listarProductos()
    {
        return $this->success(
            Product::alias('P')
                ->select(
                    "P.Nombre_Comercial",
                    "P.Codigo_Cum",
                    DB::raw("IF(ifnull(CONCAT(P.Nombre_Comercial, P.Cantidad, P.Unidad_Medida, P.Principio_Activo, P.Presentacion, P.Concentracion),'') = '',
                    P.Nombre_Comercial,
                    CONCAT(P.Nombre_Comercial,' ', P.Cantidad,' ', P.Unidad_Medida, ' (',P.Principio_Activo, ' ', P.Presentacion, ' ', P.Concentracion,')'
                    )) as Nombre"),
                    "P.Nombre_Comercial",
                    "P.Laboratorio_Comercial",
                    "P.Laboratorio_Generico",
                    "P.Id_Producto",
                    "P.Embalaje",
                    "P.Cantidad_Presentacion",
                    DB::raw("IFNULL(cp.Costo_Promedio,0) AS Costo")
                )
                ->leftJoin("Costo_Promedio AS cp", function ($join) {
                    $join->on("cp.Id_Producto", "P.Id_Producto");
                })
                ->whereNotNull("P.Codigo_Barras")
                ->whereRaw("P.Estado='Activo'")
                /* ->whereRaw("P.Codigo_Barras!=''") */
                ->where(function ($query) {
                    $query->whereRaw("P.Embalaje NOT LIKE 'MUESTRA MEDICA%'")
                        ->orWhereNull("P.Embalaje")->orWhereRaw("P.Embalaje=''");
                })
                /* ->when(request()->get("nombre"), function ($q, $fill) {
                $q->where(function($query) use ($fill){
                    $query->where("P.Principio_Activo",'like','%'.$fill.'%')
                    ->orWhere("P.Presentacion",'like','%'.$fill.'%')
                    ->orWhere("P.Concentracion",'like','%'.$fill.'%')
                    ->orWhere("P.Nombre_Comercial",'like','%'.$fill.'%')
                    ->orWhere("P.Cantidad",'like','%'.$fill.'%')
                    ->orWhere("P.Unidad_Medida",'like','%'.$fill.'%');
                });
            })
            ->when(request()->get("lab_com"), function ($q, $fill) {
                $q->where("P.Laboratorio_Comercial",'like','%'.$fill.'%');
            })
            ->when(request()->get("lab_gen"), function ($q, $fill) {
                $q->where("P.Laboratorio_Generico",'like','%'.$fill.'%');
            })
            ->when(request()->get("cum"), function ($q, $fill) {
                $q->where("P.Codigo_Cum",'like','%'.$fill.'%');
            }) */
                ->when(request()->get("categoria"), function ($q, $fill) {
                    $q->where("P.Id_Categoria", '=', $fill);
                })
                ->when(request()->get("subcategoria"), function ($q, $fill) {
                    $q->where("P.Id_Subcategoria", '=', $fill);
                })
                ->get()
        );
    }

    public function getEstados()
    {
        $listaRaw = explode(",", DB::table('information_schema.COLUMNS')
            ->selectRaw("substr(left(column_type,LENGTH(column_type)-1),6) AS lista_estados")
            ->whereRaw('CONCAT_WS("-",table_schema,TABLE_NAME,COLUMN_NAME)=?', [env('DB_DATABASE') . "-producto-estado"])
            ->first()->lista_estados);
        $lista = [];
        foreach ($listaRaw as $value) {
            $lista[] = ["estado" => str_replace("'", "", $value)];
        }
        return $this->success($lista);
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

    public function cambiarEstado(Request $request)
    {
        try {
            Product::where('Id_Producto', $request->id)->update(['Estado' => $request->estado]);
            return  $this->success('Producto ' . (($request->estado == "Inactivo") ? 'anulado' : 'activado') . ' con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getFile() . " - " . $th->getMessage());
        }
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
            $data = $request->except(["camposCategoria", "camposSubcategoria","user_id", "camposFlag"]);

            $variablesProd = [
                "cat" => [
                    "registros" =>$request->camposCategoria,
                    "modelo" => CategoryVariable::all(),
                    "campo_id" => "category_variables_id",
                    "titulo" => "categoría",
                    "camposFlag" => $request->camposFlag["cat"]
                ],
                "subcat" => [
                    "registros" =>$request->camposSubcategoria,
                    "modelo" => SubcategoryVariable::all(),
                    "campo_id" => "subcategory_variables_id",
                    "titulo" => "subcategoría",
                    "camposFlag" => $request->camposFlag["subcat"]
                ]
            ];


            $type = $request->Foto["type"];
            if (!is_null($type)) {
                if (in_array($type, ['jpeg', 'jpg', 'png'])) {
                    $base64 = saveBase64($request->Foto["file"], 'fotos_productos/', true, '.' . $type);
                    $url = URL::to('/') . '/api/image?path=' . $base64;
                } else {
                    throw new Exception(
                        "No se ha encontrado un formato de imagen válido ($type), revise e intente nuevamente"
                    );
                }
                $data['Imagen'] = $url;
            }
            unset($data["Foto"]);

            $data_actual=(isset($request->Id_Producto))?Product::find($request->Id_Producto):[];

            $product = Product::updateOrCreate(["Id_Producto" => $data["Id_Producto"]], $data);

            if(isset($data["Unidad_Medida"])){
                $data_actual["Unidad_Medida"]=Unit::find($data_actual["Unidad_Medida"])->name;
                $data["Unidad_Medida"]=Unit::find($data["Unidad_Medida"])->name;
            }
            if($product->wasRecentlyCreated){
                ActividadProducto::create([
                    "Id_Producto" => $product->Id_Producto,
                    "Person_Id" => $request->user_id,
                    "Detalles" => "El producto '".$product->Nombre_Comercial."' fue ingresado al sistema",
                ]);
            }else{
                foreach($data as $key=>$campo){
                    if($campo !== $data_actual[$key]){
                        ActividadProducto::create([
                            "Id_Producto" => $product->Id_Producto,
                            "Person_Id" => $request->user_id,
                            "Detalles" => (isset($data_actual[$key]))?"El campo '".$key."' fue modificado de '".$data_actual[$key]."' a '".$campo."'.":
                                "El campo '".$key."' fue ingresado con el valor '".$campo."'.",
                        ]);
                    }
                }
            }

            foreach ($variablesProd as $variableProd) {
                foreach ($variableProd["registros"] as $dataVariablesProd) {
                    $data_actual=(isset($d["id"]))?VariableProduct::find($d["id"]):[];

                    $dataVariablesProd["product_id"] = $product->Id_Producto;
                    $varProd=VariableProduct::updateOrCreate(['id' => $dataVariablesProd["id"]], $dataVariablesProd);

                    $nomVar=$variableProd["modelo"]->fresh()->find($varProd[$campo["campo_id"]])["label"];

                    if($varProd->wasRecentlyCreated){   // Si la variable de producto no tenia un valor antes.
                        ActividadProducto::create([
                            "Id_Producto" => $product->Id_Producto,
                            "Person_Id" => $request->user_id,
                            "Detalles" => "La variable de ".$variableProd["titulo"]." '$nomVar' fue ".
                            (($variableProd["camposFlag"])?"creada e ":"")."ingresada al sistema con el valor '".$varProd->valor."'.",
                        ]);
                    }else{
                        foreach($dataVariablesProd as $key_dataVariablesProd=>$campo_dataVariablesProd){
                            if($campo_dataVariablesProd !== $data_actual[$key]){
                                ActividadProducto::create([
                                    "Id_Producto" => $product->Id_Producto,
                                    "Person_Id" => $request->user_id,
                                    "Detalles" => "La variable de ".$variableProd["titulo"]." '$nomVar' fue ".
                                        ((isset($data_actual[$key_dataVariablesProd]))?"modificada de '".$data_actual[$key_dataVariablesProd]."' a '":"ingresada con el valor '").$campo_dataVariablesProd."'.",
                                ]);
                            }
                        }
                    }
                }
            }
            /* $data = $request->except(["dynamic"]);

            $dynamic = request()->get("dynamic");
            $product = Product::create($data);
            foreach ($dynamic as $d) {
                $d["product_id"] = $product->id;
                VariableProduct::create($d);
            }

            $data['product_id'] = $product->id;
            $data['product_dotation_type_id'] = $data["Producto_Dotation_Type_Id"];
            $data['name'] = $data["Nombre_Comercial"];
            $data['code'] = $data["Codigo"];
            $data['type'] = $data["Tipo"];
            $data['status'] = $data["Status"];
            $data['cost'] = 0;
            $data['stock'] = 0;

            $product = InventaryDotation::create($data); */

            return $this->success("guardado con éxito");
        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
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
        try {
            /* $datos = $request->all();
            $data = $request->except(["dynamic","Status","Codigo","Producto_Dotacion_Tipo","id_inventary_dotations"]);
            $dynamic = request()->get("dynamic");
            // var_dump($dynamic);
            $product = Product::where('Id_Producto', $id)->update($data);

            foreach ($dynamic as $d) {
                $d['product_id'] = $id;
                VariableProduct::updateOrCreate(['id' => $d["id"]], $d);
            }

            $datos['product_id'] = $datos["Id_Producto"];
            $datos['product_dotation_type_id'] = $datos["Producto_Dotation_Type_Id"];
            $datos['name'] = $datos["Nombre_Comercial"];
            $datos['code'] = $datos["Codigo"];
            $datos['type'] = $datos["Tipo"];
            $datos['status'] = $datos["Status"];
            $datos['cost'] = 0;
            $datos['stock'] = 0;

            InventaryDotation::updateOrCreate(['id' => $datos["id_inventary_dotations"]],$datos); */

            return $this->success("guardado con éxito");
        } catch (\Throwable $th) {
            return $this->error(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
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
