<?php

namespace App\Http\Controllers;

use App\Models\InventaryDotation;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Product;
use App\Models\VariableProduct;
use Illuminate\Support\Facades\DB;


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

        $tipoCatalogo = Request()->get('tipo');

        $data = DB::table('producto as p')->join('Subcategoria as s', 's.Id_Subcategoria', 'p.Id_Subcategoria')
            ->join('Categoria_Nueva as c', 'c.Id_Categoria_Nueva', 's.Id_Categoria_Nueva')
            ->leftJoin('product_dotation_types as pdt', 'pdt.id', 'p.Producto_Dotation_Type_Id')
            ->leftJoin('inventary_dotations as ido', 'ido.product_id', 'p.Id_Producto')
            ->select(
                'p.Id_Producto',
                'p.Codigo_Cum',
                'p.Codigo_Cum as Cum',
                'p.Principio_Activo',
                'p.Descripcion_ATC',
                'p.Codigo_Barras',
                'p.Id_Producto',
                'p.Id_Categoria',
                'p.Id_Subcategoria',
                'p.Laboratorio_Generico as Generico',
                'p.Laboratorio_Comercial as Comercial',
                'p.Invima as Invima',
                'p.Imagen as Foto',
                'p.Producto_Dotation_Type_Id',
                'p.Nombre_Comercial as Nombre_Comercial',
                'p.Id_Producto',
                'p.Embalaje',
                'p.Tipo as Tipo',
                'p.Tipo_Catalogo',
                'p.Id_Tipo_Activo_Fijo',
                'ido.status',
                'ido.id as id_inventary_dotations',
                'ido.code as code_inventary_dotations',
                'p.Referencia'
            );


        /*  if ($tipoCatalogo == 'Medicamento' || $tipoCatalogo == 'Material' ) { */
        # code...
        $data->selectRaw('
            CONCAT(
                ifnull(p.Principio_Activo,""), " ",
                ifnull(p.Presentacion,""), " ",
                ifnull(p.Concentracion,""), " ",
                ifnull(p.Nombre_Comercial,"")," ",
                ifnull(p.Unidad_Medida,""),
                ifnull(p.Embalaje,"")
                ) as Nombre,

                s.Nombre as Subcategoria,
                c.Nombre as Categoria

                 ');
        /*    } */



        return $this->success(
            $data->when(request()->get("tipo"), function ($q, $fill) {
                $q->where("p.Tipo_Catalogo", $fill);
            })
                ->when(request()->get("company_id"), function ($q, $fill) {
                    $q->where("p.company_id", $fill);
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function listarProductos(){
        return $this->success(
           Product::from('producto as P')
            ->select(
                "P.Nombre_Comercial","P.Codigo_Cum",
                DB::raw("IF(ifnull(CONCAT(P.Nombre_Comercial, P.Cantidad, P.Unidad_Medida, P.Principio_Activo, P.Presentacion, P.Concentracion),'') = '',
                    P.Nombre_Comercial,
                    CONCAT(P.Nombre_Comercial,' ', P.Cantidad,' ', P.Unidad_Medida, ' (',P.Principio_Activo, ' ', P.Presentacion, ' ', P.Concentracion,')'
                    )) as Nombre"),
                "P.Nombre_Comercial","P.Laboratorio_Comercial","P.Laboratorio_Generico","P.Id_Producto","P.Embalaje","P.Cantidad_Presentacion",
                DB::raw("IFNULL(cp.Costo_Promedio,0) AS Costo")
            )
            ->leftJoin("costo_promedio AS cp", function($join){
                $join->on("cp.Id_Producto","P.Id_Producto");
            })
            ->whereNotNull("P.Codigo_Barras")
            ->where("P.Estado","=","Activo")
            ->where("P.Codigo_Barras", "!=", "''")
            ->where(function($query){
                $query->where("P.Embalaje","NOT LIKE","MUESTRA MEDICA%")
                ->orWhereNull("P.Embalaje")->orWhere("P.Embalaje","''");
            })
            ->when(request()->get("nom"), function ($q, $fill) {
                $q->where("P.Principio_Activo",'like','%'.$fill.'%')
                ->orWhere("P.Presentacion",'like','%'.$fill.'%')
                ->orWhere("P.Concentracion",'like','%'.$fill.'%')
                ->orWhere("P.Nombre_Comercial",'like','%'.$fill.'%')
                ->orWhere("P.Cantidad",'like','%'.$fill.'%')
                ->orWhere("P.Unidad_Medida",'like','%'.$fill.'%');
            })
            ->when(request()->get("lab_com"), function ($q, $fill) {
                $q->where("P.Laboratorio_Comercial",'like','%'.$fill.'%');
            })
            ->when(request()->get("lab_gen"), function ($q, $fill) {
                $q->where("P.Laboratorio_Generico",'like','%'.$fill.'%');
            })
            ->when(request()->get("cum"), function ($q, $fill) {
                $q->where("P.Codigo_Cum",'like','%'.$fill.'%');
            })
            ->get()
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

            $data = $request->except(["dynamic"]);

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

            $product = InventaryDotation::create($data);

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
            $datos = $request->all();
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

            InventaryDotation::updateOrCreate(['id' => $datos["id_inventary_dotations"]],$datos);

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
