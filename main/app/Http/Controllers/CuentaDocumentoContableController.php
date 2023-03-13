<?php

namespace App\Http\Controllers;

use App\Models\CuentaDocumentoContable;
use Illuminate\Http\Request;
use App\Http\Services\PaginacionData;
use App\Http\Services\HttpResponse;
use App\Http\Services\consulta;
use App\Http\Services\complex;
use App\Http\Services\Utility;
use App\Http\Services\Contabilizar;
use App\Http\Services\SystemConstant;
use App\Http\Services\QueryBaseDatos;

class CuentaDocumentoContableController extends Controller
{
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
     * @param  \App\Models\CuentaDocumentoContable  $cuentaDocumentoContable
     * @return \Illuminate\Http\Response
     */
    public function show(CuentaDocumentoContable $cuentaDocumentoContable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CuentaDocumentoContable  $cuentaDocumentoContable
     * @return \Illuminate\Http\Response
     */
    public function edit(CuentaDocumentoContable $cuentaDocumentoContable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CuentaDocumentoContable  $cuentaDocumentoContable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CuentaDocumentoContable $cuentaDocumentoContable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CuentaDocumentoContable  $cuentaDocumentoContable
     * @return \Illuminate\Http\Response
     */
    public function destroy(CuentaDocumentoContable $cuentaDocumentoContable)
    {
        //
    }

    public function listaNotasCartera()
    {
        $condicion = $this->getStrCondicionsNotasCartera();

        $having = '';

        if (isset($_REQUEST['tercero']) && $_REQUEST['tercero'] != '') {
            $having .= " HAVING (Beneficiario LIKE '$_REQUEST[tercero]%' OR Tercero LIKE '$_REQUEST[tercero]%')";
        }

        $query = "SELECT
            DATE_FORMAT(NC.Fecha_Documento, '%d/%m/%Y') AS Fecha,
            NC.Codigo,
            NC.Beneficiario,
            (
            CASE
            NC.Tipo_Beneficiario
            WHEN 'Cliente' THEN (SELECT Nombre FROM Cliente WHERE Id_Cliente = NC.Beneficiario)
            WHEN 'Proveedor' THEN (SELECT Nombre FROM Proveedor WHERE Id_Proveedor = NC.Beneficiario)
            WHEN 'Funcionario' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Beneficiario)
            END
            ) AS Tercero,
            NC.Concepto,
            GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
            SUM(CDC.Debito) AS Total_Debe_PCGA,
            SUM(CDC.Credito) AS Total_Haber_PCGA,
            SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
            SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
            (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Identificacion_Funcionario) AS Funcionario
            FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Nota Cartera' $condicion GROUP BY CDC.Id_Documento_Contable $having";
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);
        //dd($query);

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

        $query = "SELECT
            CDC.Id_Documento_Contable,
            NC.Estado,
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
            NC.Concepto,
            GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
            SUM(CDC.Debito) AS Total_Debe_PCGA,
            SUM(CDC.Credito) AS Total_Haber_PCGA,
            SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
            SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
            (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Identificacion_Funcionario) AS Funcionario
            FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Nota Cartera' $condicion GROUP BY CDC.Id_Documento_Contable $having ORDER BY NC.Fecha_Registro DESC LIMIT $limit,$tamPag ";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        $response['Notas'] = $resultado;
        $response['numReg'] = $numReg;

        return json_encode($response);
    }

    function getStrCondicionsNotasCartera()
    {
        $condicion = '';

        if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != '') {
            $condicion .= " AND NC.Codigo LIKE '%$_REQUEST[cod]%'";
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

    public function listaEgresos()
    {
        $condicion = $this->getStrCondicionsEgresos();

        $having = '';

        if (isset($_REQUEST['cli']) && $_REQUEST['cli'] != '') {
            $having .= " HAVING (Beneficiario LIKE '$_REQUEST[cli]%' OR Tercero LIKE '$_REQUEST[cli]%')";
        }

        $query = "SELECT
        DATE_FORMAT(NC.Fecha_Documento, '%d/%m/%Y') AS Fecha,
        NC.Codigo,
        NC.Beneficiario,
        (
        CASE
        NC.Tipo_Beneficiario
        WHEN 'Cliente' THEN (SELECT Nombre FROM Cliente WHERE Id_Cliente = NC.Beneficiario)
        WHEN 'Proveedor' THEN (SELECT Nombre FROM Proveedor WHERE Id_Proveedor = NC.Beneficiario)
        WHEN 'Funcionario' THEN (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Beneficiario)
        END
        ) AS Tercero,
        (SELECT name FROM companies WHERE id = NC.Id_Empresa) AS Empresa,
        NC.Estado,
        NC.Concepto,
        GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
        SUM(CDC.Debito) AS Total_Debe_PCGA,
        SUM(CDC.Credito) AS Total_Haber_PCGA,
        SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
        SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
        (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Identificacion_Funcionario) AS Funcionario
        FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Egreso' $condicion GROUP BY CDC.Id_Documento_Contable $having";

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

        $query = "SELECT
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
    NC.Estado,
    GROUP_CONCAT(CDC.Cheque SEPARATOR ' | ') AS Cheques,
    SUM(CDC.Debito) AS Total_Debe_PCGA,
    SUM(CDC.Credito) AS Total_Haber_PCGA,
    SUM(CDC.Deb_Niif) AS Total_Debe_NIIF,
    SUM(CDC.Cred_Niif) AS Total_Haber_NIIF,
    (SELECT CONCAT_WS(' ', first_name, first_surname) FROM people WHERE id = NC.Identificacion_Funcionario) AS Funcionario
    FROM Cuenta_Documento_Contable CDC INNER JOIN Documento_Contable NC ON NC.Id_Documento_Contable = CDC.Id_Documento_Contable WHERE NC.Tipo = 'Egreso' $condicion GROUP BY CDC.Id_Documento_Contable $having ORDER BY 1 DESC LIMIT $limit,$tamPag ";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);

        $response['Lista'] = $resultado;
        $response['numReg'] = $numReg;

        return json_encode($response);
    }


    function getStrCondicionsEgresos()
    {
        $condicion = '';

        if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != '') {
            $condicion .= " AND NC.Codigo LIKE '%$_REQUEST[cod]%'";
        }

        if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
            $condicion .= " AND (DATE(NC.Fecha_Documento) BETWEEN '$fecha_inicio' AND '$fecha_fin')";
        }

        /* if (isset($_REQUEST['cli']) && $_REQUEST['cli'] != '') {
        $condicion .= " AND NC.Beneficiario = '$_REQUEST[cli]'";
    } */

        if (isset($_REQUEST['cheque']) && $_REQUEST['cheque'] != '') {
            $condicion .= " AND CDC.Cheque LIKE '%$_REQUEST[cheque]%'";
        }

        if (isset($_REQUEST['est']) && $_REQUEST['est'] != '') {
            $condicion .= " AND NC.Estado LIKE '$_REQUEST[est]'";
        }

        if (isset($_REQUEST['empresa']) && $_REQUEST['empresa'] != '') {
            $condicion .= " AND NC.Id_Empresa LIKE '$_REQUEST[empresa]'";
        }
        return $condicion;
    }


    public function listaComprobantes()
    {
        $tipo_comprobante = (isset($_REQUEST['tipo_comprobante']) && $_REQUEST['tipo_comprobante']) ? $_REQUEST['tipo_comprobante'] : '';

        $condicion = '';

        if (isset($_REQUEST['cod']) && $_REQUEST['cod']) {
            $condicion .= "WHERE C.Codigo LIKE '%$_REQUEST[cod]%'";
        }
        if ($condicion != "") {
            if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] != "") {
                $condicion .= " AND FP.Nombre='$_REQUEST[tipo]'";
            }
        } else {
            if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] != "") {
                $condicion .= "WHERE FP.Nombre='$_REQUEST[tipo]'";
            }
        }
        if ($condicion != "") {
            if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
                $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
                $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
                $condicion .= " AND C.Fecha_Comprobante BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            }
        } else {
            if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
                $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
                $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
                $condicion .= "WHERE C.Fecha_Comprobante BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            }
        }
        if ($condicion != "") {
            if (isset($_REQUEST['cli']) && $_REQUEST['cli'] != "") {
                $condicion .= " AND CL.Nombre LIKE '%$_REQUEST[cli]%'";
            }
        } else {
            if (isset($_REQUEST['cli']) && $_REQUEST['cli'] != "") {
                $condicion .= "WHERE CL.Nombre LIKE '%$_REQUEST[cli]%'";
            }
        }

        if ($condicion != "") {
            if (isset($_REQUEST['est']) && $_REQUEST['est'] != "") {
                $condicion .= " AND C.Estado LIKE '$_REQUEST[est]'";
            }
        } else {
            if (isset($_REQUEST['est']) && $_REQUEST['est'] != "") {
                $condicion .= "WHERE C.Estado LIKE '$_REQUEST[est]'";
            }
        }

        if ($condicion != "") {
            if ($tipo_comprobante != '') {
                $condicion .= " AND C.Tipo='" . ucwords($tipo_comprobante) . "'";
            }
        } else {
            if ($tipo_comprobante != '') {
                $condicion .= " WHERE C.Tipo='" . ucwords($tipo_comprobante) . "'";
            }
        }

        $query = 'SELECT
			C.*,
			(SELECT F.image FROM people F WHERE F.id=C.Id_Funcionario ) as Imagen,
			FP.Nombre as Forma_Pago ,
			IFNULL(CL.Nombre, (SELECT CONCAT_WS(" ",first_name,first_surname) FROM people WHERE id = C.Id_Cliente)) as Cliente  ,
			IFNULL(P.Nombre, (SELECT CONCAT_WS(" ",first_name,first_surname) FROM people WHERE id = C.Id_Proveedor)) as Proveedor
			FROM Comprobante C
            LEFT JOIN Cliente CL
            ON C.Id_Cliente=CL.Id_Cliente
            LEFT JOIN Proveedor P
            ON C.Id_Proveedor=P.Id_Proveedor
            INNER JOIN Forma_Pago FP
            ON C.Id_Forma_Pago=FP.Id_Forma_Pago ' . $condicion;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $total = $oCon->getData();
        unset($oCon);

        ####### PAGINACIÓN ########
        $tamPag = 30;
        $numReg = count($total);
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


        $query = 'SELECT
			C.*,
			(SELECT F.image FROM people F WHERE F.id=C.Id_Funcionario ) as Imagen ,
			(SELECT F.image FROM people F WHERE F.id=C.Id_Funcionario ) as Imagen ,
			FP.Nombre as Forma_Pago ,
			IFNULL(CL.Nombre, (SELECT CONCAT_WS(" ",first_name,first_surname) FROM people WHERE id = C.Id_Cliente)) as Cliente  ,
			IFNULL(P.Nombre, (SELECT CONCAT_WS(" ",first_name,first_surname) FROM people WHERE id = C.Id_Proveedor)) as Proveedor,
			FP.Nombre as Forma_Pago
		    FROM Comprobante C
            LEFT JOIN Cliente CL
            ON C.Id_Cliente=CL.Id_Cliente
            LEFT JOIN Proveedor P
            ON C.Id_Proveedor=P.Id_Proveedor
            INNER JOIN Forma_Pago FP
            ON C.Id_Forma_Pago=FP.Id_Forma_Pago ' . $condicion . ' ORDER BY C.Fecha_Registro DESC LIMIT ' . $limit . ',' . $tamPag;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        $datos['Lista'] = $resultado;
        $datos['numReg'] = $numReg;

        return json_encode($datos);
    }
}
