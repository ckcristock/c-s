<?php

namespace App\Http\Controllers;

use App\Models\DocumentoContable;
use Illuminate\Http\Request;
use App\Http\Services\PaginacionData;
use App\Http\Services\HttpResponse;
use App\Http\Services\consulta;
use App\Http\Services\complex;
use App\Http\Services\Utility;
use App\Http\Services\Contabilizar;
use App\Http\Services\SystemConstant;
use App\Http\Services\QueryBaseDatos;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

include(app_path() . '/Http/Services/comprobantes/ObtenerProximoConsecutivo.php');

class DocumentoContableController extends Controller
{

    public function paginate()
    {
        $condicion = $this->getStrCondicions();
        $having = '';

        if (isset($_REQUEST['tercero']) && $_REQUEST['tercero'] != '') {
            $having .= " HAVING (Beneficiario LIKE '$_REQUEST[tercero]%' OR Tercero LIKE '$_REQUEST[tercero]%')";
        }

        $query = " SELECT NC.Estado,
        DATE_FORMAT(NC.Fecha_Documento, '%d/%m/%Y') AS Fecha,
        NC.Codigo,
        NC.Beneficiario,
        (
            CASE
            NC.Tipo_Beneficiario
            WHEN 'Cliente' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Cliente WHERE Id_Cliente = NC.Beneficiario)
            WHEN 'Proveedor' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Proveedor WHERE Id_Proveedor = NC.Beneficiario)
            WHEN 'Funcionario' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Beneficiario)
            END
            ) AS Tercero,
            (SELECT name FROM companies WHERE id = NC.Id_Empresa) AS Empresa,
        NC.Concepto,
        GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
        SUM(CDC.Debito) AS Total_Debe_PCGA,
        SUM(CDC.Credito) AS Total_Haber_PCGA,
        SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
        SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
        (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Identificacion_Funcionario) AS Funcionario
        FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Nota Contable' $condicion GROUP BY CDC.Id_Documento_Contable $having";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        ####### PAGINACIÓN ########
        $tamPag = 20;
        $numReg = count($resultado);
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

        $query = " SELECT
        NC.Estado,
        CDC.Id_Documento_Contable,
        DATE_FORMAT(NC.Fecha_Documento, '%d/%m/%Y') AS Fecha,
        NC.Codigo,
        NC.Beneficiario,
        (
            CASE
            NC.Tipo_Beneficiario
            WHEN 'Cliente' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Cliente WHERE Id_Cliente = NC.Beneficiario)
            WHEN 'Proveedor' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Proveedor WHERE Id_Proveedor = NC.Beneficiario)
            WHEN 'Funcionario' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Beneficiario)
            END
            ) AS Tercero,
            (SELECT name FROM companies WHERE id = NC.Id_Empresa) AS Empresa,
        NC.Concepto,
        GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
        SUM(CDC.Debito) AS Total_Debe_PCGA,
        SUM(CDC.Credito) AS Total_Haber_PCGA,
        SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
        SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
        (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE identifier = NC.Identificacion_Funcionario) AS Funcionario
        FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Nota Contable' $condicion GROUP BY CDC.Id_Documento_Contable $having ORDER BY NC.Fecha_Registro DESC LIMIT $limit,$tamPag ";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        $response['Notas'] = $resultado;
        $response['numReg'] = $numReg;

        return json_encode($response);
    }

    public function getCodigo()
    {
        $mes = isset($_REQUEST['Fecha']) && $_REQUEST['Fecha'] != '' ? date('m', strtotime($_REQUEST['Fecha'])) : date('m');
        $anio = isset($_REQUEST['Fecha']) && $_REQUEST['Fecha'] != '' ? date('Y', strtotime($_REQUEST['Fecha'])) : date('Y');

        $consecutivo = obtenerProximoConsecutivo('Nota', 1);

        return json_encode([
            "consecutivo" => $consecutivo
        ]);
    }

    function getStrCondicions()
    {
        $condicion = '';

        if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != '') {
            $condicion .= " AND NC.Codigo LIKE '%$_REQUEST[cod]%'";
        }
        if (isset($_REQUEST['empresa']) && $_REQUEST['empresa'] != '') {
            $condicion .= " AND NC.Id_Empresa LIKE '%$_REQUEST[empresa]%'";
        }

        if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
            $condicion .= " AND (DATE(NC.Fecha_Documento) BETWEEN '$fecha_inicio' AND '$fecha_fin')";
        }

        /* if (isset($_REQUEST['tercero']) && $_REQUEST['tercero'] != '') {
            $condicion .= " AND NC.Beneficiario = '$_REQUEST[tercero]'";
        } */

        if (isset($_REQUEST['est']) && $_REQUEST['est'] != '') {
            $condicion .= " AND NC.Estado = '$_REQUEST[est]'";
        }

        return $condicion;
    }

    public function nitBuscar()
    {
        $query = '(
            SELECT C.Id_Cliente AS ID, IF(Nombre IS NULL OR Nombre = "", CONCAT_WS(" ", C.Id_Cliente,"-",Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), CONCAT(C.Id_Cliente, " - ", C.Nombre)) AS Nombre, "Cliente" AS Tipo FROM Cliente C WHERE C.Estado != "Inactivo")
            UNION (SELECT P.Id_Proveedor AS ID, IF(P.Nombre = "" OR P.Nombre IS NULL, CONCAT_WS(" ",P.Id_Proveedor,"-",P.Primer_Nombre,P.Segundo_Nombre,P.Primer_Apellido,P.Segundo_Apellido),CONCAT(P.Id_Proveedor, " - ", P.Nombre)) AS Nombre, "Proveedor" AS Tipo FROM Proveedor P WHERE P.Estado != "Inactivo")
            UNION (SELECT F.id AS ID, CONCAT(F.id, " - ", F.first_name," ", F.first_surname) AS Nombre, "Funcionario" AS Tipo FROM people F
            )';
        // UNION (SELECT CC.nit AS ID, CONCAT(CC.nit, " - ", CC.name) AS Nombre, "Caja_Compensacion" AS Tipo FROM compensation_funds CC WHERE CC.nit IS NOT NULL

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $proveedorbucar = $oCon->getData();
        unset($oCon);


        return json_encode($proveedorbucar);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentoContable  $documentoContable
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentoContable $documentoContable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocumentoContable  $documentoContable
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentoContable $documentoContable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentoContable  $documentoContable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentoContable $documentoContable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DocumentoContable  $documentoContable
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentoContable $documentoContable)
    {
        //
    }

    public function subirFacturas()
    {
        $http_response = new HttpResponse();


        if (!empty($_FILES['archivo']['name'])) { // Archivo de la archivo de Entrega.
            $posicion1 = strrpos($_FILES['archivo']['name'], '.') + 1;
            $extension1 =  substr($_FILES['archivo']['name'], $posicion1);
            $extension1 =  strtolower($extension1);
            $_filename1 = uniqid() . "." . $extension1;
            $_file1 = app_path() . "ARCHIVOS/COMPROBANTES/" . $_filename1;

            //$subido1 = move_uploaded_file($_FILES['archivo']['tmp_name'], $_file1);
        }

        $inputFileName = app_path() . "ARCHIVOS/COMPROBANTES/" . $_filename1;

        try {

            $inputFileType = Excel::identify($inputFileName); // PHPExcel_IOFactory::identify($inputFileName);
            $objReader = Excel::createReader($inputFileType); //PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'I';

        $facturas = [];
        $i = -1;
        for ($row = 1; $row <= $highestRow; $row++) {
            $i++;
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);
            $facturas[$i] = $rowData;
        }
        $facturas_no_encontradas = [];
        $retenciones = [];
        $descuentos = [];
        $ajustes = [];
        $fact = [];
        $f = 0;
        $a = 0;
        $d = 0;
        $i = 0;
        $r = 0;
        $x = 0;
        $y = 0;


        foreach ($facturas as $value) {

            foreach ($value as $key => $item) {
                if ($item[0] != '' && $item[1] != '') { // Si el plan y el nit son diferentes de vacío consultamos.
                    $valido = true;


                    $datosTercero = $this->getDatosTercero($item[1]);

                    $plan_cuentas = $this->getDatosPlanCuenta($item[0]);
                    # $id_plan_cuentas = getIdPlanCuenta($item[0]);


                    $centrocosto = $this->getDetalleCentroCosto($item[2]);
                    //$id_centro_costo = getIdCentroCosto($item[2]);

                    if (!$datosTercero || !$plan_cuentas || !$centrocosto) {
                        # code...
                        $valido = false;
                    }

                    $factura = [
                        #"Id_Plan_Cuentas" => $id_plan_cuentas,
                        "Id_Plan_Cuentas" => $plan_cuentas['Id_Plan_Cuentas'],
                        #"Cuenta" => getDatosPlanCuenta($item[0]),
                        "Cuenta" => $plan_cuentas,
                        "Nit_Cuenta" => $item[1],
                        "Nit" => $datosTercero,
                        "Tipo_Nit" => $datosTercero['Tipo'],
                        #"Id_Centro_Costo" =>$id_centro_costo,
                        "Id_Centro_Costo" => $centrocosto['Id_Centro_Costo'],
                        #"Centro_Costo" => getDetalleCentroCosto($item[2]),
                        "Centro_Costo" => $centrocosto,
                        "Documento" => $item[3],
                        "Concepto" => $item[4],
                        "Base" => '0',
                        "Debito" => $item[5] != '' ? str_replace(",", ".", $item[5]) : '0',
                        "Credito" => $item[6] != '' ? str_replace(",", ".", $item[6]) : '0',
                        "Deb_Niif" => $item[7] != '' ? str_replace(",", ".", $item[7]) : '0',
                        "Cred_Niif" => $item[8] != '' ? str_replace(",", ".", $item[8]) : '0',
                        "Valido" => $valido
                    ];

                    $fact[] = $factura;
                }
            }
            if ($x == 200) {

                $logFile = fopen('prueba.txt', 'w') or die("Error creando archivo");;
                fwrite($logFile, $y);
                $y++;
                sleep(5);
                $x = 0;
            }

            $x++;
        }

        $file = app_path() . "ARCHIVOS/COMPROBANTES/" . $_filename1;
        unlink($file);
        $resultado['Facturas'] = $fact;

        return json_encode($resultado);
    }

    function getIdPlanCuenta($codigo)
    {
        $query = "SELECT P.Id_Plan_Cuentas FROM Plan_Cuentas P WHERE P.Codigo='$codigo'";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        return $resultado ? $resultado['Id_Plan_Cuentas'] : '0';
    }

    function getDatosPlanCuenta($codigo)
    {
        $query = 'SELECT PC.Id_Plan_Cuentas, PC.Id_Plan_Cuentas AS Id,
        PC.Codigo, PC.Codigo AS Codigo_Cuenta,/*  CONCAT(PC.Nombre," - ",PC.Codigo) as Codigo,  */
        CONCAT(PC.Codigo," - ",PC.Nombre) as Nombre, PC.Centro_Costo
        FROM Plan_Cuentas PC WHERE PC.Codigo = "' . $codigo . '"';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        return $resultado ? $resultado : [];
    }

    function getDatosTercero($nit)
    {
        $query = 'SELECT r.* FROM (
            (
            SELECT C.Id_Cliente AS ID, IF(Nombre IS NULL OR Nombre = "", CONCAT_WS(" ", C.Id_Cliente,"-",Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido),
             CONCAT(C.Id_Cliente, " - ", C.Nombre)) AS Nombre, "Cliente" AS Tipo FROM Cliente C WHERE C.Estado != "Inactivo" AND C.Id_Cliente = ' . $nit . ' )

            UNION (SELECT P.Id_Proveedor AS ID, IF(P.Nombre = "" OR P.Nombre IS NULL,
                CONCAT_WS(" ",P.Id_Proveedor,"-",P.Primer_Nombre,P.Segundo_Nombre,P.Primer_Apellido,P.Segundo_Apellido),CONCAT(P.Id_Proveedor, " - ", P.Nombre)) AS Nombre,
                "Proveedor" AS Tipo FROM Proveedor P
                WHERE  P.Id_Proveedor = ' . $nit . '
                )

            UNION (SELECT F.Identificacion_Funcionario AS ID, CONCAT(F.Identificacion_Funcionario, " - ", F.Nombres," ", F.Apellidos) AS Nombre,
                "Funcionario" AS Tipo FROM Funcionario F
                WHERE  F.Identificacion_Funcionario  = ' . $nit . '
                )
            UNION (SELECT CC.Nit AS ID, CONCAT(CC.Nit, " - ", CC.Nombre) AS Nombre, "Caja_Compensacion" AS Tipo FROM Caja_Compensacion CC
                WHERE CC.Nit IS NOT NULL AND
                CC.Nit  = ' . $nit . '
                )
            )   r ';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        return $resultado ? $resultado : [];
    }

    function getIdCentroCosto($codigo_centro_costo)
    {

        $id_centro_costo = '0';

        if ($codigo_centro_costo != '') {
            $query = "SELECT Id_Centro_Costo FROM Centro_Costo WHERE Codigo LIKE '%$codigo_centro_costo' LIMIT 1";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $resultado = $oCon->getData();

            if ($resultado) {
                $id_centro_costo = $resultado['Id_Centro_Costo'];
            }
        }

        return $id_centro_costo;
    }

    function getDetalleCentroCosto($codigo_centro_costo)
    {

        $res = [];

        if ($codigo_centro_costo != '') {
            $query = 'SELECT CONCAT(Codigo, " - ", Nombre) AS Nombre, Id_Centro_Costo FROM Centro_Costo WHERE Movimiento = "Si" AND Estado = "Activo" AND Codigo LIKE "%' . $codigo_centro_costo . '" LIMIT 1';

            $oCon = new consulta();
            $oCon->setQuery($query);
            $centrocosto = $oCon->getData();
            unset($oCon);

            if ($centrocosto) {
                $res = $centrocosto;
            }
        }

        return $res;
    }

    public function descargarPdf()
    {
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        $tipo = (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');
        $titulo = 'CONT. PCGA';

        if ($tipo != '') {
            $titulo = 'CONT. NIFF';
        }
        $oItem = new complex('Configuracion', "Id_Configuracion", 1);
        $config = $oItem->getData();
        unset($oItem);
        /* FIN DATOS GENERALES DE CABECERAS Y CONFIGURACION */


        ob_start(); // Se Inicializa el gestor de PDF

        /* HOJA DE ESTILO PARA PDF*/
        $style = '<style>
        .page-content{
        width:750px;
        }
        .row{
        display:inlinie-block;
        width:750px;
        }
        .td-header{
            font-size:15px;
            line-height: 20px;
        }
        .titular{
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        </style>';
        /* FIN HOJA DE ESTILO PARA PDF*/

        /* HAGO UN SWITCH PARA TODOS LOS MODULOS QUE PUEDEN GENERAR PDF */

        $query = "SELECT
        NC.*,
        (
        CASE
        NC.Tipo_Beneficiario
        WHEN 'Cliente' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Cliente WHERE Id_Cliente = NC.Beneficiario)
        WHEN 'Proveedor' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Proveedor WHERE Id_Proveedor = NC.Beneficiario)
        WHEN 'people' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Beneficiario)
        END
        ) AS Tercero,
        (SELECT name FROM companies) AS Empresa,
        (SELECT document_number FROM companies) AS document_number,
        (SELECT document_type FROM companies) AS document_type,
        (SELECT IFNULL(Nombre,'Sin Centro Costo') FROM Centro_Costo WHERE Id_Centro_Costo = NC.Id_Centro_Costo) AS Centro_Costo
        FROM Documento_Contable NC WHERE NC.Id_Documento_Contable = $id";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $data = $oCon->getData();
        unset($oCon);


        $query = "SELECT PC.Codigo, PC.Nombre AS Cuenta, PC.Nombre_Niif AS Cuenta_Niif, PC.Codigo_Niif, CNC.Concepto, CNC.Documento, CNC.Nit, (
            CASE
            CNC.Tipo_Nit
            WHEN 'Cliente' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Cliente WHERE Id_Cliente = CNC.Nit)
            WHEN 'Proveedor' THEN (SELECT IF(Nombre IS NULL OR Nombre = '', CONCAT_WS(' ', Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), Nombre) FROM Proveedor WHERE Id_Proveedor = CNC.Nit)
            WHEN 'people' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = CNC.Nit)
            END
            ) AS Tercero,
            (SELECT IFNULL(Nombre,'Sin Centro Costo') FROM Centro_Costo WHERE Id_Centro_Costo = CNC.Id_Centro_Costo) AS Centro_Costo, CNC.Debito, CNC.Credito, CNC.Cred_Niif, CNC.Deb_Niif FROM Cuenta_Documento_Contable CNC INNER JOIN Plan_Cuentas PC ON CNC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas WHERE CNC.Id_Documento_Contable = $id";
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $cuentas = $oCon->getData();
        unset($oCon);


        $oItem = new complex('people', "id", $data["Identificacion_Funcionario"]);
        $elabora = $oItem->getData();
        unset($oItem);

        $codigos = '
            <h3 style="margin:5px 0 0 0;font-size:22px;line-height:22px;">' . $data["Codigo"] . '</h3>
            <h4 style="margin:5px 0 0 0;font-size:18px;line-height:22px;">' . $titulo . '</h4>
            <h5 style="margin:5px 0 0 0;font-size:16px;line-height:16px;">' . $this->fecha($data["Fecha_Documento"]) . '</h5>
        ';

        $contenido_centro_costo = '';

        if ($data['Id_Centro_Costo'] != '' && $data['Id_Centro_Costo'] != '0') {
            $contenido_centro_costo .= '
            <tr style=" min-height: 100px;
            background: #e6e6e6;
            padding: 15px;
            border-radius: 10px;
            margin: 0;">

                <td style="font-size:11px;font-weight:bold;width:100px;padding:5px">
                Centro Costo:
                </td>

                <td style="font-size:11px;width:610px;padding:5px">
                ' . $data['Centro_Costo'] . '
                </td>

            </tr>
            ';
        }

        $contenido = '<table style="background: #e6e6e6;">
            <tr style=" min-height: 100px;
            background: #e6e6e6;
            padding: 15px;
            border-radius: 10px;
            margin: 0;">

                <td style="font-size:11px;font-weight:bold;width:100px;padding:5px">
                Beneficiario:
                </td>

                <td style="font-size:11px;width:610px;padding:5px">
                ' . $data['Tercero'] . '
                </td>

            </tr>

            <tr style=" min-height: 100px;
            background: #e6e6e6;
            padding: 15px;
            border-radius: 10px;
            margin: 0;">

                <td style="font-size:11px;font-weight:bold;width:100px;padding:5px">
                Documento:
                </td>

                <td style="font-size:11px;width:610px;padding:5px">
                ' . $data['Beneficiario'] . '
                </td>

            </tr>

            <tr style=" min-height: 100px;
            background: #e6e6e6;
            padding: 15px;
            border-radius: 10px;
            margin: 0;">

                <td style="font-size:11px;font-weight:bold;width:100px;padding:5px">
                Concepto:
                </td>

                <td style="font-size:11px;width:610px;padding:5px">
                ' . $data['Concepto'] . '
                </td>

            </tr>
            ' . $contenido_centro_costo . '
        </table>
        ';

        $contenido .= '<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:60px;max-width:60px;font-weight:bold;background:#cecece;;border:1px solid #cccccc;">
            Codigo ' . $tipo . '
        </td>
        <td style="width:90px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
           Cuenta ' . $tipo . '
        </td>
        <td style="width:130px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
           Concepto
        </td>
        <td style="width:70px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Doc.
        </td>
        <td style="width:100px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
           Centro Costo
        </td>
        <td style="width:100px;max-width:100px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Nit
        </td>
        <td style="width:70px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Debito ' . $tipo . '
        </td>
        <td style="width:70px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Credito ' . $tipo . '
        </td>
    </tr>';

        $totalDeb = 0;
        $totalCred = 0;

        foreach ($cuentas as $cuenta) {

            if ($tipo != '') {
                $codigo = $cuenta->Codigo_Niif;
                $nombre_cuenta = $cuenta->Cuenta_Niif;
                $debe = $cuenta->Deb_Niif;
                $haber = $cuenta->Cred_Niif;
            } else {
                $codigo = $cuenta->Codigo;
                $nombre_cuenta = $cuenta->Cuenta;
                $debe = $cuenta->Debito;
                $haber = $cuenta->Credito;
            }

            $documento = $cuenta->Documento;
            $documento = wordwrap($documento, 17, "<br />\n", true);

            $contenido .= '<tr>
        <td style="vertical-align:center;font-size:9px;width:50px;max-width:50px;text-align:center;border:1px solid #cccccc;">
            ' . $codigo . '
        </td>
        <td style="vertical-align:center;font-size:9px;width:90px;border:1px solid #cccccc;">
            ' . $nombre_cuenta . '
        </td>
        <td style="vertical-align:center;font-size:9px;width:84px;border:1px solid #cccccc;">
            ' . $cuenta->Concepto . '
        </td>
        <td style="vertical-align:center;font-size:9px;word-break:break-all;width:60px;max-width:60px;border:1px solid #cccccc;">
            ' . $documento . '
        </td>
        <td style="vertical-align:center;font-size:9px;width:100px;border:1px solid #cccccc;">
            ' . $cuenta->Centro_Costo . '
        </td>
        <td style="width:100px;max-width:100px;font-size:9px;word-break:break-all;border:1px solid #cccccc;">
            ' . $cuenta->Tercero . ' - ' . $cuenta->Nit . '
        </td>
        <td style="vertical-align:center;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
            $.' . number_format($debe, 2, '.', ',') . '
        </td>
        <td style="vertical-align:center;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
            $.' . number_format($haber, 2, '.', ',') . '
        </td>
    </tr>';

            $totalDeb += $debe;
            $totalCred += $haber;
        }

        $contenido .= '<tr>
    <td colspan="6" style="padding:4px;text-align:left;border:1px solid #cccccc;font-weight:bold;font-size:12px">Totales:</td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $.' . number_format($totalDeb, 2, ".", ",") . '
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $.' . number_format($totalCred, 2, ".", ",") . '
    </td>
    </tr>';

        $contenido .= '</table>

    <table style="margin-top:10px;" cellpadding="0" cellspacing="0">

        <tr>
            <td style="font-weight:bold;width:170px;border:1px solid #cccccc;padding:4px">
                Elaboró:
            </td>
            <td style="font-weight:bold;width:168px;border:1px solid #cccccc;padding:4px">
                Revisó:
            </td>
            <td style="font-weight:bold;width:168px;border:1px solid #cccccc;padding:4px">
                Aprobó:
            </td>
            <td style="font-weight:bold;width:168px;border:1px solid #cccccc;padding:4px">
                Beneficiario
            </td>
        </tr>

        <tr>
            <td style="font-size:10px;width:170px;border:1px solid #cccccc;padding:4px">
                ' /* . $elabora['first_surname'] . ' ' . $elabora['Nombres'] */ . '
            </td>
            <td style="width:168px;border:1px solid #cccccc;padding:4px">

            </td>
            <td style="width:168px;border:1px solid #cccccc;padding:4px">

            </td>
            <td style="width:168px;border:1px solid #cccccc;padding:4px">

            </td>
        </tr>

    </table>


    ';


        /* if ($data["Codigo_Qr"] == '' || !file_exists($nombre_fichero)) {
            $cabecera3 .= '<img src="' . $_SERVER["DOCUMENT_ROOT"] . 'assets/images/sinqr.png' . '" style="max-width:100%;margin-top:-10px;" />';
        } else {
            $cabecera3 .= '<img src="' . $nombre_fichero . '" style="max-width:100%;margin-top:-10px;" />';
        } */


        /* CABECERA GENERAL DE TODOS LOS ARCHIVOS PDF*/
        $cabecera = '<table style="" >
              <tbody>
                <tr>
                  <td style="width:70px;">

                  </td>
                  <td class="td-header" style="width:390px;font-weight:thin;font-size:14px;line-height:20px;">
                    ' . $data["Empresa"] . '<br>
                    ' . $data["document_type"] . '.: ' . $data["document_number"] . '<br>
                  </td>
                  <td style="width:170px;text-align:right">
                        ' . $codigos . '
                  </td>
                  <td style="width:100px;">

                  </td>
                </tr>
              </tbody>
            </table><hr style="border:1px dotted #ccc;width:730px;">';
        /* FIN CABECERA GENERAL DE TODOS LOS ARCHIVOS PDF*/

        $marca_agua = '';

        if ($data['Estado'] == 'Anulada') {
            $marca_agua = 'backimg="' . $_SERVER["DOCUMENT_ROOT"] . 'assets/images/anulada.png"';
        }

        /* CONTENIDO GENERAL DEL ARCHIVO MEZCLANDO TODA LA INFORMACION*/
        $content = '<page backtop="0mm" backbottom="0mm" ' . $marca_agua . '>
                <div class="page-content" >' .
            $cabecera .
            $contenido . '
                </div>
            </page>';
        return $content;
    }

    function fecha($str)
    {
        $parts = explode(" ", $str);
        $date = explode("-", $parts[0]);
        return $date[2] . "/" . $date[1] . "/" . $date[0];
    }
}
