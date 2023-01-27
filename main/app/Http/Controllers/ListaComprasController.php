<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompraNacional;
use App\Models\Perfil;
use App\Models\Person;
use App\Models\PreCompra;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ThirdParty;
use App\Traits\ApiResponser;
use Hamcrest\Core\IsTypeOf;
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
      $this->proveedores = ThirdParty::name("social_reason")->whereRaw("third_party_type = 'Proveedor'");
    }

    public function index(){
        return $this->success(
            /* DB::table("Orden_Compra_Nacional","OCN") */
            OrdenCompraNacional::alias("OCN")
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
                $join->on("pr.id","=","OCN.Id_Proveedor");
            })
            ->join("people as p",function($join){
                $join->on("p.id","=","OCN.Identificacion_Funcionario");
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

    public function datosComprasNacionales() {
        /* $query = DB::table("Orden_Compra_Nacional","OCN") */
        $query = OrdenCompraNacional::alias("OCN")
        ->select([
            "ocn.Codigo",
            "ocn.Fecha AS Fecha_Compra",
            "ocn.Tipo",
            "ocn.Estado",
            "ocn.Aprobacion",
            "pr.social_reason AS Proveedor",
            DB::raw("IFNULL(b.Nombre,IFNULL(pd.Nombre,'Ninguno')) AS Bodega,
            DATE_FORMAT(ocn.Fecha_Entrega_Probable, '%d/%m/%Y') AS Fecha_Probable"),
            "ocn.Observaciones",
            "ocn.Codigo_Qr"
        ])
        ->joinSub($this->proveedores,"pr",function($join){
            $join->on("pr.id","=","OCN.Id_Proveedor");
        })
        ->leftJoin("Bodega_Nuevo as b",function($join){
            $join->on("b.Id_Bodega_Nuevo","=","OCN.Id_Bodega_Nuevo");
        })
        ->leftJoin("Punto_Dispensacion as pd",function($join){
            $join->on("pd.Id_Punto_Dispensacion","=","OCN.Id_Punto_Dispensacion");
        })
        ->when(request()->get('id'), function ($q, $fill) {
            $q->where("OCN.Id_Orden_Compra_Nacional",$fill);
        });
        return $this->success($query->first());
    }

    public function detallesComprasNacionales() {
        $query = DB::table("Producto_Orden_Compra_Nacional as POCN")
        ->select([
            DB::raw("IF(ifnull(CONCAT(PRD.Principio_Activo, PRD.Presentacion, PRD.Concentracion, PRD.Cantidad, PRD.Unidad_Medida),'') = '',
                CONCAT(PRD.Nombre_Comercial,' ',PRD.Laboratorio_Comercial),
                CONCAT(PRD.Principio_Activo,' ', PRD.Presentacion,' ', PRD.Concentracion, ' ', PRD.Cantidad, ' ', PRD.Unidad_Medida))
            as Nombre_Producto"),
            "PRD.Embalaje","PRD.Nombre_Comercial",
            "POCN.Costo as Costo" ,
            "POCN.Cantidad as Cantidad",
            "POCN.Iva as Iva",
            "POCN.Total as Total",
            "POCN.Id_Producto as Id_Producto",
            "PRD.Cantidad_Presentacion AS Presentacion",
            DB::raw("'0' as Rotativo")
        ])
        ->join("Producto as PRD",function($join){
            $join->on("PRD.Id_Producto","=","POCN.Id_Producto");
        })
        ->when(request()->get('id'), function ($q, $fill) {
            $q->where("POCN.Id_Orden_Compra_Nacional",$fill);
        });

        return $this->success($query->get());
    }

    public function actividadOrdenCompra(){
        $query = DB::table("Actividad_Orden_Compra as AR")
        ->select([
            "AR.*", "p.image as Imagen",
            DB::raw("(CASE
                WHEN AR.Estado='Creacion' THEN CONCAT('1 ',AR.Estado)
                WHEN AR.Estado='Edicion' THEN CONCAT('2 ',AR.Estado)
                WHEN AR.Estado='Recepcion' THEN CONCAT('3 ',AR.Estado)
                WHEN AR.Estado='Aprobacion' THEN CONCAT('4 ',AR.Estado)
                WHEN AR.Estado='Anulada' THEN CONCAT('2 ',AR.Estado)
            END) as Estado2")
        ])
        ->join("people as p",function($join){
            $join->on("p.id", "AR.Identificacion_Funcionario");
        })
        ->when(request()->get('id'), function ($q, $fill) {
            $q->where("AR.Id_Orden_Compra_Nacional",$fill);
        })
        ->orderBy('Fecha', 'desc')
        ->orderBy('Estado2', 'asc');
        return $this->success($query->get());
    }

    public function detallePerfil(){
        $query = Perfil::alias("PE")
        ->select(["PE.*", "PF.*"])
        ->join("Perfil_Funcionario as PF",function($join){
            $join->on("PF.Id_Perfil", "PE.Id_Perfil");
        })
        ->whereIn("PE.Id_Perfil",[1,29,16,44])// 1 Administrador General, 29 es Gerente Compras, 16 Administrador, 44 Grerente Comercial.
        ->when(request()->get('funcionario'), function ($q, $fill) {
            $q->where("PF.Identificacion_Funcionario",$fill);
        });

        $numregQuery=count($query->get()->toArray()); // Si hay registros significa que tiene permisos.
        return $this->success(["status" => ($numregQuery>0)]);
    }

    public function detalleRechazo(){
        return $this->success(DB::table("Tipo_Rechazo")->get());
    }

    public function detallePreCompra($id)
    {
        $encabezado = DB::table("Pre_Compra","PC")
        ->select(
            "PC.*", "pr.nit",
            DB::raw("ifnull(pr.social_reason,concat(
                pr.first_name,
                IF(isnull(pr.second_name) = 0,' ',''),
                ifnull(pr.second_name,''),
                ' ',
                pr.first_surname,
                IF(isnull(pr.second_surname) = 0,' ',''),
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

        $productos = DB::table("Producto_Pre_Compra","PPC")
        ->join("Producto as p", function ($join) {
            $join->on("p.Id_Producto", "PPC.Id_Producto");
        })
        ->when($id, function ($q, $fill) {
            $q->where('POCN.Id_Pre_Compra', $fill);
        });
        return $this->success(["Datos" =>$encabezado->get(), "Proveedor" =>$proveedor->get(), "Productos" =>$productos->get()]);
    }

    public function preCompras(){
        return $this->success(
            PreCompra::as("PC")
            ->select(
                "PC.*",
                "p.image",
                "pr.social_reason as Nombre ",
                DB::raw("CONCAT('OP',ifnull(OP.Id_Orden_Pedido,'')) as Orden_Pedido"))
            ->join("people as p",function($join){
                $join->on("p.id", "PC.Identificacion_Funcionario");
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
              IF(isnull(second_name) = 0,' ',''),
                ifnull(second_name,''),
                ' ',
                first_surname,
              IF(isnull(second_surname) = 0,' ',''),
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
            PreCompra::where('Id_Pre_Compra',request()->get('id_pre_compra'))
            ->update(['Estado' => 'Solicitada'])
        );
    }

    public function storeCompra(){
        try{
            $datos=request()->get('datos');
            $productos = $datos['Productos'];
            unset($datos['Productos']);
            /* $result = DB::table("Orden_Compra_Nacional")->updateOrInsert( */
            $result = OrdenCompraNacional::createOrInsert(
                [ 'Id_Orden_Compra_Nacional'=> $datos['Id_Orden_Compra_Nacional'] ],
                $datos
            );
            /* $ordenNueva = (!isset($datos['Id_Orden_Compra_Nacional']));
            $result=($ordenNueva)?
                DB::table('Orden_Compra_Nacional')
                ->latest('Id_Orden_Compra_Nacional')->first()
            :
                DB::table('Orden_Compra_Nacional')
                ->where('Id_Orden_Compra_Nacional',$datos['Id_Orden_Compra_Nacional'])->get();  */

            $ordenNueva = $result->wasRecentlyCreated;
            /* DB::table("Orden_Compra_Nacional")
            -> */
            OrdenCompraNacional::where('Id_Orden_Compra_Nacional',$result->Id_Orden_Compra_Nacional)
            ->update(['Codigo' => "OC".$result->Id_Orden_Compra_Nacional]);

            if(request()->get('id_pre_compra')){
                DB::table("Pre_Compra")->updateOrInsert(
                    ['Id_Pre_Compra' => request()->get('id_pre_compra')],
                    ['Id_Orden_Compra_Nacional' => $result->Id_Orden_Compra_Nacional]
                );
            }

            foreach ($productos as $producto) {
                $producto['Id_Orden_Compra_Nacional'] = $result->Id_Orden_Compra_Nacional;
                DB::table("Producto_Orden_Compra_Nacional")->updateOrInsert(
                    ['Id_Producto_Orden_Compra_Nacional' => $producto['Id_Producto']],
                    $producto
                );
            }

            DB::table("Actividad_Orden_Compra")->insert(
                [
                    'Id_Orden_Compra_Nacional' => $result->Id_Orden_Compra_Nacional,
                    'Identificacion_Funcionario' => $result->Identificacion_Funcionario,
                    'Detalles' => "Se ".(($ordenNueva) ? 'creó' : 'editó')." la orden de compra con codigo OC".$result->Id_Orden_Compra_Nacional,
                    'Fecha' => date("Y-m-d H:i:s"),
                    'Estado' => ($ordenNueva) ? 'Creacion' : 'Edicion'
                ]
            );

            return $this->success('Orden de compra '.(($ordenNueva) ? 'creada' : 'actualizada').' con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( [ "file" => $th->getFile().":".$th->getLine(),"err" => $th->getCode(),"data" => $th->getMessage()]);
        }
    }

    public function getEstadosCompra(){
        $listaRaw=explode(",",DB::table('information_schema.COLUMNS')
        ->selectRaw("substr(left(column_type,LENGTH(column_type)-1),6) AS lista_estados")
        ->whereRaw('CONCAT_WS("-",table_schema,TABLE_NAME,COLUMN_NAME)=?',[env('DB_DATABASE')."-orden_compra_nacional-estado"])
        ->first()->lista_estados);
        $lista=[];
        foreach($listaRaw as $value){
            $lista[]=["estado"=>str_replace("'","",$value)];
        }
        return $this->success($lista);
    }

    public function setEstadoCompra(){
        try{
            /* DB::table("Orden_Compra_Nacional")
            -> */
            OrdenCompraNacional::where('Id_Orden_Compra_Nacional',request()->get("id"))
            ->update([ 'Estado' => request()->get("estado") ]);


            $motivo="";
            if(in_array(request()->get("estado"),['Aprobada', 'Rechazada'])){
                if(request()->get("motivo") != ""){
                    $motivo=" con el siguiente motivo: ".DB::table("Tipo_Rechazo")
                        ->where("Id_Tipo_Rechazo",request()->get("motivo"))->first()->Nombre;
                }

                DB::table("Actividad_Orden_Compra")->insert(
                    [
                        'Id_Orden_Compra_Nacional' => request()->get("id"),
                        'Identificacion_Funcionario' => request()->get("funcionario"),
                        'Detalles' => 'Esta orden de compra ha sido '.strtolower(request()->get("estado")).' exitosamente'.$motivo.'.',
                        'Fecha' => date("Y-m-d H:i:s"),
                        'Estado' => (request()->get("estado")=="Aprobada") ? 'Aprobacion' : 'Rechazada'
                    ]
                );
            }

            return $this->success([
                "mensaje" => 'Esta orden de compra ha sido '.strtolower(request()->get("estado")).' exitosamente'.$motivo.'.',
                "tipo" => "success",
                "titulo" => "Operación exitosa"
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse([
                "file" => $th->getFile().":".$th->getLine(),
                "err" => $th->getCode(),
                "data" => [
                    "mensaje" => $th->getMessage(),
                    "tipo" => "error",
                    "titulo" => "Error en la operación"
                ]
            ]);
        }
    }
}
