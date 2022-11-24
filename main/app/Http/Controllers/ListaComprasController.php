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

    /* public function index2()
    {
        $condicion = '';
        $funcionario = (isset($_REQUEST['funcionario']) ? $_REQUEST['funcionario'] : '');
        $tipo = (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');

        if (isset($_REQUEST['est']) && $_REQUEST['est'] != "") {
            $condicion .= "WHERE OCN.Estado='$_REQUEST[est]'";
        }

        if ($condicion != "") {
            if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != "") {
                $condicion .= " AND OCN.Codigo LIKE '%$_REQUEST[cod]%'";
            }
        } else {
            if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != "") {
                $condicion .= "WHERE OCN.Codigo LIKE '%$_REQUEST[cod]%'";
            }
        }
        if ($condicion != "") {
            if (isset($_REQUEST['prov']) && $_REQUEST['prov'] != "") {
                $condicion .= " AND P.Nombre LIKE '%$_REQUEST[prov]%'";
            }
        } else {
            if (isset($_REQUEST['prov']) && $_REQUEST['prov'] != "") {
                $condicion .= "WHERE P.Nombre LIKE '%$_REQUEST[prov]%'";
            }
        }
        if ($condicion != "") {
            if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "" && $_REQUEST['fecha'] != "undefined") {
                $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
                $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
                $condicion .= " AND DATE_FORMAT(Fecha, '%Y-%m-%d') BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            }
        } else {
            if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "" && $_REQUEST['fecha'] != "undefined") {
                $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
                $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
                $condicion .= "WHERE DATE_FORMAT(Fecha, '%Y-%m-%d') BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            }
        }

        if ($condicion != "") {
            if ($tipo != '' && $tipo == 'filtrado') {
                if ($funcionario != '') {
                    $condicion .= " AND OCN.Identificacion_Funcionario = " . $funcionario . " ";
                }
            }
        } else {
            if ($tipo != '' && $tipo == 'filtrado') {
                if ($funcionario != '') {
                    $condicion .= " WHERE OCN.Identificacion_Funcionario = " . $funcionario . " ";
                }
            }
        }

        if (isset($_REQUEST['func']) && $_REQUEST['func'] != "" && $_REQUEST['func'] != "undefined") {

            if ($condicion != "") {
                $condicion .= " (AND OCN.Identificacion_Funcionario = '" . $_REQUEST['func'] . "' OR  F.Nombres LIKE '%" . $_REQUEST['func'] . "%')";
            } else {
                $condicion .= " WHERE ( OCN.Identificacion_Funcionario = '" . $_REQUEST['func'] . "' OR  F.Nombres LIKE '%" . $_REQUEST['func'] . "%') ";
            }
        }



        $query = 'SELECT COUNT(*) AS Total
          FROM Orden_Compra_Nacional OCN
          INNER JOIN Proveedor P ON OCN.Id_Proveedor=P.Id_Proveedor
          INNER JOIN Funcionario F ON F.Identificacion_Funcionario = OCN.Identificacion_Funcionario '
            . $condicion
            . ' ORDER BY OCN.Fecha DESC';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $total = $oCon->getData();
        unset($oCon);

        ####### PAGINACIÓN ########
        $tamPag = 20;
        $numReg = $total["Total"];
        $paginas = ceil($numReg / $tamPag);
        $limit = "";
        $paginaAct = "";

        if (!isset($_REQUEST['pag']) || $_REQUEST['pag'] == '') {
            $paginaAct = 1;
            $limit = 0;
        } else {
            $paginaAct = $_REQUEST['pag'];
            $limit = ($paginaAct - 1) * $tamPag;
        }

        $query = 'SELECT OCN.*, P.Nombre as Proveedor, F.Imagen FROM Orden_Compra_Nacional OCN INNER JOIN Proveedor P ON OCN.Id_Proveedor=P.Id_Proveedor INNER JOIN Funcionario F ON OCN.Identificacion_Funcionario=F.Identificacion_Funcionario ' . $condicion . ' ORDER BY OCN.Fecha DESC LIMIT ' . $limit . ',' . $tamPag;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado['compras'] = $oCon->getData();
        $resultado['conteo_compras'] = count($resultado['compras']);
        unset($oCon);

        $resultado['numReg'] = $numReg;

        return response()->json($resultado);
    } */

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

}
