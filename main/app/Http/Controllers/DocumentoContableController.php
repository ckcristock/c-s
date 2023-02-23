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

    public function nitBuscar(){
        $query = '(
            SELECT C.Id_Cliente AS ID, IF(Nombre IS NULL OR Nombre = "", CONCAT_WS(" ", C.Id_Cliente,"-",Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido), CONCAT(C.Id_Cliente, " - ", C.Nombre)) AS Nombre, "Cliente" AS Tipo FROM Cliente C WHERE C.Estado != "Inactivo")
            UNION (SELECT P.Id_Proveedor AS ID, IF(P.Nombre = "" OR P.Nombre IS NULL, CONCAT_WS(" ",P.Id_Proveedor,"-",P.Primer_Nombre,P.Segundo_Nombre,P.Primer_Apellido,P.Segundo_Apellido),CONCAT(P.Id_Proveedor, " - ", P.Nombre)) AS Nombre, "Proveedor" AS Tipo FROM Proveedor P WHERE P.Estado != "Inactivo")
            UNION (SELECT F.id AS ID, CONCAT(F.id, " - ", F.first_name," ", F.first_surname) AS Nombre, "Funcionario" AS Tipo FROM people F
            )';
            // UNION (SELECT CC.nit AS ID, CONCAT(CC.nit, " - ", CC.name) AS Nombre, "Caja_Compensacion" AS Tipo FROM compensation_funds CC WHERE CC.nit IS NOT NULL

        $oCon= new consulta();
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
}
