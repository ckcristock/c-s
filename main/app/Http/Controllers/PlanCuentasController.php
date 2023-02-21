<?php

namespace App\Http\Controllers;

use App\Exports\PlanCuentasExport;
use App\Models\PlanCuentas;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\PaginacionData;
use App\Http\Services\HttpResponse;
use App\Http\Services\consulta;
use App\Http\Services\complex;
use App\Http\Services\QueryBaseDatos;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PlanCuentasController extends Controller
{

    use ApiResponser;

    public function paginate(Request $request)
    {
        $pagina = (isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '');
        $condicion = $this->SetCondiciones($_REQUEST);
        $query_paginacion = 'SELECT COUNT(*) AS Total
                        FROM Plan_Cuentas'
            . $condicion;
        $query = 'SELECT pc.*, CONCAT(pc.Codigo, " - ", pc.Nombre) as code, c.name as Empresa
                FROM Plan_Cuentas as pc
                LEFT JOIN companies as c ON c.id = pc.Id_Empresa'
            . $condicion . ' ORDER BY Codigo';
        $paginationObj = new PaginacionData(20, $query_paginacion, $pagina);
        $queryObj = new QueryBaseDatos($query);
        $result = $queryObj->Consultar('Multiple', true, $paginationObj);

        return json_encode($result);
    }


    public function SetCondiciones($req)
    {
        $condicion = '';

        if (isset($req['nombre']) && $req['nombre'] != "") {
            $condicion .= " WHERE Nombre LIKE '%" . $req['nombre'] . "%'";
        }

        if (isset($req['nombre_niif']) && $req['nombre_niif']) {
            if ($condicion != "") {
                $condicion .= " AND Nombre_Niif LIKE '%" . $req['nombre_niif'] . "%'";
            } else {
                $condicion .=  " WHERE Nombre_Niif LIKE '%" . $req['nombre_niif'] . "%'";
            }
        }

        if (isset($req['cod']) && $req['cod']) {
            if ($condicion != "") {
                $condicion .= " AND Codigo LIKE '" . $req['cod'] . "%'";
            } else {
                $condicion .= " WHERE Codigo LIKE '" . $req['cod'] . "%'";
            }
        }

        if (isset($req['cod_niif']) && $req['cod_niif']) {
            if ($condicion != "") {
                $condicion .= " AND Codigo_Niif LIKE '" . $req['cod_niif'] . "%'";
            } else {
                $condicion .= " WHERE Codigo_Niif LIKE '" . $req['cod_niif'] . "%'";
            }
        }

        if (isset($req['estado']) && $req['estado']) {
            if ($condicion != "") {
                $condicion .= " AND Estado = '" . $req['estado'] . "'";
            } else {
                $condicion .= " WHERE Estado = '" . $req['estado'] . "'";
            }
        }

        if (isset($req['company_id']) && $req['company_id']) {
            if ($condicion != "") {
                $condicion .= " AND Id_Empresa = '" . $req['company_id'] . "'";
            } else {
                $condicion .= " WHERE Id_Empresa = '" . $req['company_id'] . "'";
            }
        }

        return $condicion;
    }

    public function descargarExcel(Request $request)
    {
        $plan = PlanCuentas::where('Id_Empresa', $request->id)->orderBy('Codigo')->get();
        return Excel::download(new PlanCuentasExport($plan), 'PUC.xlsx');
    }

    public function cambiarEstado()
    {
        $respuesta = array();

        $id_cuenta = (isset($_REQUEST['id_cuenta']) ? $_REQUEST['id_cuenta'] : '');
        //$id_cuenta = json_decode($id_cuenta);

        if ($id_cuenta == '') {
            $respuesta["msg"] = "No se envio un plan de cuenta para proceder, contacte con el administrador!";
            $respuesta["icon"] = "error";
            $respuesta["title"] = "Error al guardar!";
            echo json_encode($respuesta);
            return;
        }

        /* $oItem = new complex("Plan_Cuentas", "Id_Plan_Cuentas", $id_cuenta);
        $plan = $oItem->getData();
        unset($oItem);

        if ($plan["Estado"] === 'ACTIVO') {
            $oItem1 = new complex("Plan_Cuentas", "Id_Plan_Cuentas", $id_cuenta);
            $oItem1->Estado = 'INACTIVO';
            $oItem1->save();
            unset($oItem1);
        } elseif ($plan["Estado"] === 'INACTIVO') {
            $oItem2 = new complex("Plan_Cuentas", "Id_Plan_Cuentas", $id_cuenta);
            $oItem2->Estado = 'ACTIVO';
            $oItem2->save();
            unset($oItem2);
        } */

        PlanCuentas::where('Id_Plan_Cuentas', $id_cuenta)
            ->update(['Estado' => DB::raw("IF(Estado = 'ACTIVO', 'INACTIVO', 'ACTIVO')")]);


        $respuesta["msg"] = "Se ha modificado corretamente el estado del plan de cuentas";
        $respuesta["icon"] = "success";
        $respuesta["title"] = "¡Cambio exitoso!";

        return json_encode($respuesta);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function listarBancos()
    {
        $query = 'SELECT * FROM banks ORDER BY name ASC';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        return json_encode($resultado);
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
    public function store()
    {
        $datos = isset($_REQUEST['Datos']) ? $_REQUEST['Datos'] : false;

        $datos = json_decode($datos, true);

        $guardar = true;

        //var_dump($datos);
        //exit;

        $oItem = '';

        if (isset($datos['Id_Plan_Cuentas']) && $datos['Id_Plan_Cuentas'] != '') {
            $oItem = new complex("Plan_Cuentas", "Id_Plan_Cuentas", $datos['Id_Plan_Cuentas']);
        } else {
            $oItem = new complex("Plan_Cuentas", "Id_Plan_Cuentas");
        }

        foreach ($datos as $index => $value) {
            if ($index == 'Codigo' || $index == 'Codigo_Niif') {
                if (!isset($datos['Id_Plan_Cuentas'])) { // Si no es un proceso de edici��n
                    if ($this->validarPUC($value, $datos["company_id"])) { // Si existe un PUC con el mismo c��digo que no se guarde.
                        $guardar = false;
                        break;
                    }
                }
            }
            if ($index == 'company_id') {
                $oItem->Id_Empresa = $value;
            }

            $oItem->$index = $value;
        }
        $oItem->Codigo = $datos["Codigo_Niif"];
        $oItem->Nombre = $datos["Nombre_Niif"];
        $oItem->Tipo_P = $datos["Tipo_Niif"];
        if ($guardar) {

            $oItem->save();
            $id_plan = $oItem->getId();
            unset($oItem);

            if ($id_plan) {
                $resultado['mensaje'] = "Plan de cuenta registrado satisfactoriamente.";
                $resultado['tipo'] = "success";
            } else {
                $resultado['mensaje'] = "Ha ocurrido un error de conexion, comunicarse con el soporte tecnico.";
                $resultado['tipo'] = "error";
            }
        } else {
            $resultado['mensaje'] = "Ya existe un PUC con ese c��digo.";
            $resultado['tipo'] = "error";
        }


        return json_encode($resultado);
    }

    function validarPUC($cod,$emp){
        $query = "SELECT Id_Plan_Cuentas FROM Plan_Cuentas WHERE Codigo = '$cod' OR Codigo_Niif = '$cod' AND Id_Empresa=$emp";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        return $resultado || false;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlanCuentas  $planCuentas
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $id_cuenta = (isset($_REQUEST['id_cuenta']) && $_REQUEST['id_cuenta'] != "") ? $_REQUEST['id_cuenta'] : '';

        $respuesta = array();
        $http_response = new HttpResponse();

        if ($id_cuenta == '') {
            $http_response->SetRespuesta(1, 'Detalle', 'No se envio el plan de cuenta para proceder, contacte con el administrador!');
            $respuesta = $http_response->GetRespuesta();
            echo json_encode($respuesta);
            return;
        }

        $query = '
        SELECT
        Plan_Cuentas.*, (SELECT name FROM banks WHERE id = Plan_Cuentas.Cod_Banco) AS Nombre_Banco
        FROM Plan_Cuentas
        WHERE
            Id_Plan_Cuentas = ' . $id_cuenta;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $plan_cuenta = $oCon->getData();
        unset($oCon);

        $http_response->SetDatosRespuesta($plan_cuenta);
        $http_response->SetRespuesta(0, 'Detalle', 'Operación Exitosa!');
        $respuesta = $http_response->GetRespuesta();
        return json_encode($respuesta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PlanCuentas  $planCuentas
     * @return \Illuminate\Http\Response
     */
    public function edit(PlanCuentas $planCuentas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlanCuentas  $planCuentas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanCuentas $planCuentas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanCuentas  $planCuentas
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanCuentas $planCuentas)
    {
        //
    }
}
