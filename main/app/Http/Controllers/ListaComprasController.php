<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\ThirdParty;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class ListaComprasController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $proveedores;

    public function __construct(){
      $this->proveedores = ThirdParty::whereRaw("third_party_type = 'Proveedor'");
    }

    public function index(){
        return $this->success(
            DB::table("Orden_Compra_Nacional","OCN")
            ->select(DB::raw("concat(
                p.first_name,
              IF(ifnull(p.second_name,'') != '',' ',''),
                ifnull(p.second_name,''),
                ' ',
                p.first_surname,
              IF(ifnull(p.second_surname,'') != '',' ',''),
                ifnull(p.second_surname,'')
            ) as Funcionario"),"OCN.*","pr.social_reason as Proveedor", "p.image")
            ->joinSub($this->proveedores,"pr",function($join){
                $join->on("pr.nit","=","OCN.Id_Proveedor");
            })
            ->join("people as p",function($join){
                $join->on("p.identifier","=","OCN.Identificacion_Funcionario");
            })
            ->when((request('tipo')=="filtrado")?request('funcionario'):false, function ($q, $fill) {
                $q->where("OCN.Identificacion_Funcionario", '=', $fill);
            })
            ->when(request()->get('est'), function ($q, $fill) {
                $q->where('OCN.Estado', '=', $fill);
            })
            ->when(request()->get('cod'), function ($q, $fill) {
                $q->where('OCN.Codigo', 'like', '%'.$fill.'%');
            })
            ->when(request()->get('prov'), function ($q, $fill) {
                $q->where('pr.social_reason', 'like', '%'.$fill.'%');
            })
            ->when(request()->get('fecha'), function ($q, $fill) {
                $q->whereBetween(DB::raw("DATE_FORMAT(Fecha, '%Y-%m-%d')"),explode(" - ",$fill));
            })
            ->when(request('func'), function ($q, $fill) {
                $q->where("OCN.Identificacion_Funcionario", '=', $fill)
                ->orWhere(DB::raw("concat(p.first_name,' ',p.first_surname)"), 'like', '%'.$fill.'%');
            })->orderByDesc("OCN.Fecha")
            ->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function detallePreCompra($id)
    {
        $encabezado = DB::table("Pre_Compra","PC")
        ->select(
            "PC.*", "pr.nit",
            DB::raw("ifnull(pr.social_reason,concat(
                pr.first_name,
                IF(ifnull(pr.second_name,'') != '',' ',''),
                ifnull(pr.second_name,''),
                ' ',
                pr.first_surname,
                IF(ifnull(pr.second_surname,'') != '',' ',''),
                ifnull(pr.second_surname,'')
            )) as NombreProveedor"))
        ->joinSub($this->proveedores,"pr", function ($join) {
            $join->on("pr.nit", "PC.Id_Proveedor");
        })
        ->when($id, function ($q, $fill) {
            $q->where('PC.Id_Pre_Compra', $fill);
        });

        $proveedor = DB::query()->select(
            "PR.nit as Id_Proveedor",
            DB::raw("CONCAT(PR.NombreProveedor,' - ',PR.nit) as NombreProveedor")
        )
        ->fromSub($encabezado,"PR");

        $productos = DB::table("Producto_Pre_Compra","POCN")
        ->join("producto as p", function ($join) {
            $join->on("p.Id_Producto", "POCN.Id_Producto");
        })
        ->when($id, function ($q, $fill) {
            $q->where('POCN.Id_Pre_Compra', $fill);
        });
        return $this->success(["Datos" =>$encabezado->get(), "Proveedor" =>$proveedor->get(), "Productos" =>$productos->get()]);
    }

    public function preCompras(){
        return $this->success(
            DB::table("Pre_Compra","PC")
            ->select(
                "PC.*",
                "p.image",
                "pr.social_reason as Nombre ",
                DB::raw("ifnull(CONCAT('OP',OP.Id_Orden_Pedido),'') as Orden_Pedido"))
            ->join("people as p",function($join){
                $join->on("p.identifier", "PC.Identificacion_Funcionario");
            })
            ->leftJoin("Orden_Pedido as OP", function ($join) {
                $join->on('OP.Id_Orden_Pedido', 'PC.Id_Orden_Pedido');
            })
            ->joinSub($this->proveedores,"pr", function ($join) {
                $join->on("pr.nit", "PC.Id_Proveedor");
            })
            ->when(request('funcionario'), function ($q, $fill) {
                $q->where("PC.Identificacion_Funcionario", $fill);
            })
            ->whereRaw("PC.Estado='Pendiente'")->orderBy("PC.Id_Pre_Compra","desc")
            ->get()
        );
    }

    public function getFuncionarios()
	{
		return $this->success(
			Person::select("id as value",
            DB::raw("concat(
                first_name,
              IF(ifnull(second_name,'') != '',' ',''),
                ifnull(second_name,''),
                ' ',
                first_surname,
              IF(ifnull(second_surname,'') != '',' ',''),
                ifnull(second_surname,'')
            ) as text")
            )
            ->when(request('depen'), function ($q, $fill) {
                $q->whereHas('work_contract', function ($q2) use ($fill) {
                    $q2->whereHas('position', function ($q3) use ($fill) {
                        $q3->whereHas('dependency', function ($q4) use ($fill) {
                            $q4->where('dependencies.name', 'like', '%'.$fill.'%');
                        });
                    });
                });
            })
            ->whereRaw("status = 'Activo'")->get()
		);
	}

    public function actualizarEstadoPreCompra($id){
        return $this->success(
            DB::table("Pre_Compra")
            ->where('Id_Pre_Compra',request()->get('id_pre_compra'))
            ->update(['Estado' => 'Solicitada'])
        );
    }

    public function storeCompra(){
        try{
            $result = DB::table("Orden_Compra_Nacional")->updateOrCreate(
                [ 'Id_Orden_Compra_Nacional'=> request()->get('id') ],
                request()->get('datos')
            );
            DB::table("Orden_Compra_Nacional")
            ->where('Id_Orden_Compra_Nacional',$result->Id_Orden_Compra_Nacional)
            ->update(['Codigo' => 'OC'.$result->Id_Orden_Compra_Nacional]);

            if(request()->get('id_pre_compra')){
                DB::table("Pre_Compra")->updateOrCreate(
                    ['Id_Pre_Compra' => request()->get('id_pre_compra')],
                    ['Id_Orden_Compra_Nacional' => $result->Id_Orden_Compra_Nacional]
                );
            }

            foreach (request()->get('productos') as $producto) {
                $producto['Id_Orden_Compra_Nacional'] = $result->Id_Orden_Compra_Nacional;
                DB::table("Producto_Orden_Compra_Nacional")->updateOrCreate(
                    ['Id_Producto_Orden_Compra_Nacional' => $producto->Id_Producto],
                    $producto
                );
            }

            if($result->wasRecentlyCreated){
                DB::table("Actividad_Orden_Compra")->updateOrCreate(
                    ['Id_Actividad_Orden_Compra' => request()->get('id_pre_compra')],
                    [
                        'Id_Orden_Compra_Nacional' => $result->Id_Orden_Compra_Nacional,
                        'Identificacion_Funcionario' => $result->Identificacion_Funcionario,
                        'Detalles' => "Se ".(($result->wasRecentlyCreated) ? 'creó' : 'editó')." la orden de compra con codigo OC".$result->Id_Orden_Compra_Nacional,
                        'Fecha' => date("Y-m-d H:i:s"),
                        'Estado' => ($result->wasRecentlyCreated) ? 'Creación' : 'Edición'
                    ]
                );
            }

            return $this->success('Orden de compra '.(($result->wasRecentlyCreated) ? 'creada' : 'actualizada').' con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( [ "file" => $th->getFile(),"text" => $th->getMessage()]);
        }
    }

}
