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
use App\Imports\AccountPlansImport;
use App\Imports\InitialBalanceImport;
use App\Models\AccountPlanBalance;
use App\Models\PrettyCash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PlanCuentasController extends Controller
{

    use ApiResponser;

    public function importCommercialPuc()
    {
        $file_path = '/imports/puc-comercial.xlsx';
        if (AccountPlanBalance::count() && AccountPlanBalance::where('balance', '<>', 0)->count()) {
            return $this->success(
                response()->json([
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'No podemos hacer esto porque ya hay saldos iniciales.',
                    'timer' => 0
                ])
            );
        } else {
            PlanCuentas::truncate();
            AccountPlanBalance::truncate();
            Excel::import(new AccountPlansImport, $file_path, 'public');
            return $this->success(
                response()->json([
                    'icon' => 'success',
                    'title' => 'Operación exitosa',
                    'text' => 'PUC comercial cargado correctamente.',
                    'timer' => 1000
                ])
            );
        }
    }
    public function setTipoCierre()
    {
        $id_plan_cuenta = isset($_REQUEST['id_plan_cuenta']) ? $_REQUEST['id_plan_cuenta'] : false;

        # MENSUAL O ANUAL
        $tipo_cierre = isset($_REQUEST['tipo_cierre']) ? $_REQUEST['tipo_cierre'] : false;

        #Costos - Gastos - Ingresos - Sin Asignar
        $valor_actualizar = isset($_REQUEST['valor_actualizar']) &&  $_REQUEST['valor_actualizar'] != '' ? $_REQUEST['valor_actualizar'] : 'Sin Asignar';

        $input  = 'Tipo_Cierre_' . $tipo_cierre;
        $oItem = new complex('Plan_Cuentas', 'Id_Plan_Cuentas', $id_plan_cuenta);
        $oItem->$input = $valor_actualizar;
        $oItem->save();
    }

    public function getPlanCuentas()
    {
        $filtros = isset($_REQUEST['filtros']) ? $_REQUEST['filtros'] : false;
        $filtros = json_decode($filtros, true);

        $tipoCierre = isset($_REQUEST['tipoCierre']) ? $_REQUEST['tipoCierre'] : false;

        $cond = '';
        if ($filtros) {
            if ($filtros['codigo'] != '') {
                # code...
                $cond .= ' AND Codigo LIKE "' . $filtros['codigo'] . '%" ';
            }
            if ($filtros['nombre'] != '') {
                # code...
                $cond .= ' AND Nombre LIKE "' . $filtros['nombre'] . '%" ';
            }
            if ($filtros['tipoCierre'] != '') {
                # code...
                $cond .= ' AND Tipo_Cierre_' . $tipoCierre . ' LIKE "' . $filtros['tipoCierre'] . '%" ';
            }
        }

        $query = '
		SELECT
            Id_Plan_Cuentas,
            Codigo,
            Nombre,
            Tipo_Cierre_' . $tipoCierre . '
		FROM Plan_Cuentas
		WHERE
			Estado = "ACTIVO" ' . $cond . '
		ORDER BY Codigo';

        //Se crea la instancia que contiene la consulta a realizar
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $planes = $oCon->getData();
        unset($oCon);

        $res['type'] = $planes ? 'success' : 'error';
        $res['query_result'] = $planes;

        //Ejecuta la consulta de la instancia queryobj y retorna el resultado de la misma segun los parametros

        return json_encode($res);
    }

    public function validateExcel(Request $request, $delete)
    {
        //dd($delete);
        $file = base64_decode(
            preg_replace(
                "#^data:application/\w+;base64,#i",
                "",
                $request->file
            )
        );
        if (AccountPlanBalance::count() && AccountPlanBalance::where('balance', '<>', 0)->count()) {
            return $this->success(
                response()->json([
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'No podemos hacer esto porque ya hay saldos iniciales.',
                    'timer' => 0
                ])
            );
        } else {
            if ($delete == 'true') {
                PlanCuentas::truncate();
                AccountPlanBalance::truncate();
            }
            $file_path = '/imports/' . Str::random(30) . time() . '.xlsx';
            Storage::disk('public')->put($file_path, $file, "public");
            Excel::import(new AccountPlansImport, $file_path, 'public');
            return $this->success(
                response()->json([
                    'icon' => 'success',
                    'title' => 'Operación exitosa',
                    'text' => 'PUC comercial cargado correctamente.',
                    'timer' => 1000
                ])
            );
        }
    }


    public function importInitialBalances(Request $request)
    {
        $file = base64_decode(
            preg_replace(
                "#^data:application/\w+;base64,#i",
                "",
                $request->file
            )
        );
        $file_path = '/imports/' . Str::random(30) . time() . '.xlsx';
        Storage::disk('public')->put($file_path, $file, "public");
        Excel::import(new InitialBalanceImport, $file_path, 'public');
        return $this->success('Operacion existosa');
    }

    public function paginate2()
    {
        return $this->success(
            PlanCuentas::with('cuenta_padre')
                ->whereRaw('LENGTH(Codigo_Niif) = 1')
                ->get(['Id_Plan_Cuentas', 'Nombre_Niif', 'Codigo_Niif', 'Codigo_Padre'])
        );
    }

    public function paginate(Request $request)
    {
        $pagina = (isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '');
        $condicion = $this->SetCondiciones($_REQUEST);
        $query_paginacion = 'SELECT COUNT(*) AS Total
                        FROM Plan_Cuentas as pc'
            . $condicion;
        $query = 'SELECT pc.*, CONCAT(pc.Codigo, " - ", pc.Nombre) as code, c.name as Empresa, pc2.Nombre as Cuenta_Padre_Nombre
                FROM Plan_Cuentas as pc
                LEFT JOIN companies as c ON c.id = pc.Id_Empresa
                LEFT JOIN Plan_Cuentas as pc2 ON pc.Codigo_Padre = pc2.Codigo_Niif'
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
            $condicion .= " WHERE pc.Nombre LIKE '%" . $req['nombre'] . "%'";
        }

        if (isset($req['nombre_niif']) && $req['nombre_niif']) {
            if ($condicion != "") {
                $condicion .= " AND pc.Nombre_Niif LIKE '%" . $req['nombre_niif'] . "%'";
            } else {
                $condicion .=  " WHERE pc.Nombre_Niif LIKE '%" . $req['nombre_niif'] . "%'";
            }
        }

        if (isset($req['cod']) && $req['cod']) {
            if ($condicion != "") {
                $condicion .= " AND pc.Codigo LIKE '" . $req['cod'] . "%'";
            } else {
                $condicion .= " WHERE pc.Codigo LIKE '" . $req['cod'] . "%'";
            }
        }

        if (isset($req['cod_niif']) && $req['cod_niif']) {
            if ($condicion != "") {
                $condicion .= " AND pc.Codigo_Niif LIKE '" . $req['cod_niif'] . "%'";
            } else {
                $condicion .= " WHERE pc.Codigo_Niif LIKE '" . $req['cod_niif'] . "%'";
            }
        }

        if (isset($req['estado']) && $req['estado']) {
            if ($condicion != "") {
                $condicion .= " AND pc.Estado = '" . $req['estado'] . "'";
            } else {
                $condicion .= " WHERE pc.Estado = '" . $req['estado'] . "'";
            }
        }

        if (isset($req['company_id']) && $req['company_id']) {
            if ($condicion != "") {
                $condicion .= " AND pc.Id_Empresa = '" . $req['company_id'] . "'";
            } else {
                $condicion .= " WHERE pc.Id_Empresa = '" . $req['company_id'] . "'";
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

    public function comprobanteCuentas()
    {
        $query = "SELECT Id_Plan_Cuentas AS value, CONCAT_WS(' ',Codigo,'-',Nombre) AS label FROM Plan_Cuentas WHERE Banco = 'S' AND Cod_Banco IS NOT NULL";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultados = $oCon->getData();
        unset($oCon);

        return json_encode($resultados);
    }

    public function filtrarCuentas()
    {
        $match = (isset($_REQUEST['coincidencia']) ? $_REQUEST['coincidencia'] : '');
        $tipo = (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');

        $http_response = new HttpResponse();

        if ($tipo == 'pcga') {
            $query = '
		SELECT
			Id_Plan_Cuentas,
			Codigo,
			Nombre,
			CONCAT(Codigo," - ", Nombre) AS Nombre_Cuenta
		FROM Plan_Cuentas
		WHERE
			(Nombre LIKE "%' . $match . '%" OR Codigo LIKE "%' . $match . '%") AND Movimiento = "S"
		ORDER BY Nombre ASC';
        } else {
            $query = '
		SELECT
			Id_Plan_Cuentas,
			Codigo_Niif,
			Nombre_Niif,
			CONCAT(Codigo_Niif," - ", Nombre_Niif) AS Nombre_Cuenta_Niif
		FROM Plan_Cuentas
		WHERE
			(Nombre_Niif LIKE "%' . $match . '%" OR Codigo_Niif LIKE "%' . $match . '%") AND Movimiento = "S"
		ORDER BY Nombre_Niif ASC';
        }


        //Se crea la instancia que contiene la consulta a realizar
        $queryObj = new QueryBaseDatos($query);

        //Ejecuta la consulta de la instancia queryobj y retorna el resultado de la misma segun los parametros
        $CodigoCIEs = $queryObj->ExecuteQuery('Multiple');

        return json_encode($CodigoCIEs);
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
        //dd(auth()->user()->id);
        $datos = isset($_REQUEST['Datos']) ? $_REQUEST['Datos'] : false;
        $datos = json_decode($datos, true);
        unset($datos['Cuenta_Padre_Nombre']);
        //dd($datos);
        $guardar = true;
        $oItem = '';
        if (isset($datos['Id_Plan_Cuentas']) && $datos['Id_Plan_Cuentas'] != '') {
            $oItem = PlanCuentas::find($datos['Id_Plan_Cuentas']);
        } else {
            $oItem = new PlanCuentas;
        }
        foreach ($datos as $index => $value) {
            if ($index == 'Codigo' || $index == 'Codigo_Niif') {
                if (!isset($datos['Id_Plan_Cuentas'])) { // Si no es un proceso de edición
                    if (PlanCuentas::where('Codigo', $value)->where('Id_Empresa', $datos["company_id"])->exists()) { // Si existe un PUC con el mismo código que no se guarde.
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
        $oItem->Movimiento = $datos['Tipo_Niif'] == 'AUXILIAR' ? 'S' : 'N';
        if ($guardar) {

            $oItem->save();
            $id_plan = $oItem->Id_Plan_Cuentas;
            $balance = AccountPlanBalance::where('account_plan_id', $id_plan)->first();
            if (!$balance) {
                AccountPlanBalance::create([
                    'account_plan_id' => $id_plan,
                    'balance' => 0
                ]);
            }
            if (strval($datos['Codigo_Padre']) === '110510') {
                PrettyCash::create([
                    'user_id' =>  Auth::id(),
                    'account_plan_id' => $id_plan,
                    'initial_balance' => 0,
                    'description' => $oItem->Nombre,
                    'status' => 'Inactiva'
                ]);
            }
            unset($oItem);

            if ($id_plan) {
                $resultado['mensaje'] = "Plan de cuenta registrado satisfactoriamente.";
                $resultado['tipo'] = "success";
            } else {
                $resultado['mensaje'] = "Ha ocurrido un error de conexion, comunicarse con el soporte tecnico.";
                $resultado['tipo'] = "error";
            }
        } else {
            $resultado['mensaje'] = "Ya existe un PUC con ese código.";
            $resultado['tipo'] = "error";
        }
        return json_encode($resultado);
    }

    function validarPUC($cod, $emp)
    {
        $query = "SELECT Id_Plan_Cuentas FROM Plan_Cuentas WHERE Codigo = '$cod' OR Codigo_Niif = '$cod' AND Id_Empresa=$emp";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        return $resultado || false;
    }

    public function validarNiveles()
    {
        $tipo_plan = $_REQUEST['Tipo_Plan'];
        $codigo = $_REQUEST['Codigo'];
        $tipo_puc = $_REQUEST['Tipo_Puc'];
        $nombre_nivel_superior = '';

        $codigo_validar = $this->getCodigoValidar($tipo_plan, $codigo);
        $campo_codigo = $tipo_puc == 'pcga' ? 'Codigo' : 'Codigo_Niif';

        $query = "SELECT Id_Plan_Cuentas FROM Plan_Cuentas WHERE $campo_codigo LIKE '$codigo_validar%'";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        $response = ["validacion" => 0, "nivel_superior" => $nombre_nivel_superior];

        if ($resultado) {
            $response['validacion'] = 1;
        }

        echo json_encode($response);
    }

    function getCodigoValidar($tipo_plan, $codigo)
    {
        $nivel_superior = $this->getNivelSuperiorLength($tipo_plan);

        $codigo = substr($codigo, 0, $nivel_superior);

        return $codigo;
    }

    function getNivelSuperiorLength($tipo_plan)
    {

        $nombre_nivel_superior = '';

        $nombres_niveles = [
            "1" => "Grupo",
            "2" => "Clase",
            "4" => "Cuenta",
            "6" => "Subcuenta"
        ];

        $niveles_superior = [
            "auxiliar" => 6,
            "subcuenta" => 4,
            "cuenta" => 2,
            "clase" => 1
        ];

        $nombre_nivel_superior = $nombres_niveles[$niveles_superior[$tipo_plan]];

        return $niveles_superior[$tipo_plan];
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

        $query = 'SELECT
        pc.*, (SELECT name FROM banks WHERE id = pc.Cod_Banco) AS Nombre_Banco, pc2.Nombre as Cuenta_Padre_Nombre
        FROM Plan_Cuentas as pc
        LEFT JOIN Plan_Cuentas as pc2 ON pc.Codigo_Padre = pc2.Codigo_Niif
        WHERE pc.Id_Plan_Cuentas = ' . $id_cuenta;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $plan_cuenta = $oCon->getData();
        unset($oCon);

        $http_response->SetDatosRespuesta($plan_cuenta);
        $http_response->SetRespuesta(0, 'Detalle', 'Operación Exitosa!');
        $respuesta = $http_response->GetRespuesta();
        return json_encode($respuesta);
    }

    public function getListaCuentasContables()
    {
        $ng_select = true;
        $query = '';

        if ($ng_select) {
            $query = "SELECT Id_Plan_Cuentas AS value, CONCAT(Codigo,' - ',Nombre) AS label FROM Plan_Cuentas WHERE Estado = 'Activo' AND Movimiento = 'S'";
        }

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $cuentas = $oCon->getData();
        unset($oCon);

        return json_encode($cuentas);
    }

    public function listaCuentas()
    {
        $company = (isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : '1');
        $query = 'SELECT PC.Id_Plan_Cuentas,
                 PC.Id_Plan_Cuentas AS Id,
                 PC.Codigo,
                 PC.Codigo AS Codigo_Cuenta,
                 CONCAT(PC.Nombre," - ",PC.Codigo) as Codigo,
                 CONCAT(PC.Codigo," - ",PC.Nombre) as Nombre,
                 PC.Centro_Costo
                 FROM Plan_Cuentas PC
                 WHERE CHAR_LENGTH(PC.Codigo)>5 AND PC.Movimiento = "S"
                 AND PC.Id_Empresa=' . $company;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado['Activo'] = $oCon->getData();
        unset($oCon);

        $query = 'SELECT PC.Id_Plan_Cuentas,
                PC.Codigo AS Codigo_Cuenta,
                CONCAT(PC.Nombre," - ",PC.Codigo) as Codigo
                FROM Plan_Cuentas PC
                WHERE CHAR_LENGTH(PC.Codigo)>5 AND PC.Movimiento = "S"
                AND PC.Id_Empresa=' . $company;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado['Pasivo'] = $oCon->getData();
        unset($oCon);

        return json_encode($resultado);
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
