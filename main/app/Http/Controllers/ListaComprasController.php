<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\consulta;
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
    public function index(){
        $proveedores = ThirdParty::where("third_party_type","'Proveedor'");
        return $this->success(
            DB::table("Orden_Compra_Nacional","OCN")
            ->selectRaw("count(OCN.*)as total")
            ->joinSub($proveedores,"p",function($join){
                $join->on("OCN.id","=","p.nit");
            })
            ->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function index2()
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

        $query = 'SELECT OCN.*, P.Nombre as Proveedor, F.Imagen FROM Orden_Compra_Nacional OCN
INNER JOIN Proveedor P ON OCN.Id_Proveedor=P.Id_Proveedor INNER JOIN Funcionario F ON OCN.Identificacion_Funcionario=F.Identificacion_Funcionario ' . $condicion . ' ORDER BY OCN.Fecha DESC LIMIT ' . $limit . ',' . $tamPag;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado['compras'] = $oCon->getData();
        $resultado['conteo_compras'] = count($resultado['compras']);
        unset($oCon);

        $resultado['numReg'] = $numReg;

        return response()->json($resultado);
    }
}
