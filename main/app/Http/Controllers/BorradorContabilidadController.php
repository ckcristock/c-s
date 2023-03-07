<?php

namespace App\Http\Controllers;

use App\Models\BorradorContabilidad;
use Illuminate\Http\Request;
use App\Http\Services\PaginacionData;
use App\Http\Services\HttpResponse;
use App\Http\Services\consulta;
use App\Http\Services\complex;
use App\Http\Services\Utility;
use App\Http\Services\Contabilizar;
use App\Http\Services\SystemConstant;
use App\Http\Services\QueryBaseDatos;

class BorradorContabilidadController extends Controller
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

    public function lista()
    {
        $tipo_comprobante = isset($_REQUEST['Tipo_Comprobante']) ? $_REQUEST['Tipo_Comprobante'] : false;
        $funcionario = isset($_REQUEST['Identificacion_Funcionario']) ? $_REQUEST['Identificacion_Funcionario'] : false;
        $resultado = [];

        if ($tipo_comprobante && $funcionario) {
            $query = "SELECT Id_Borrador_Contabilidad AS ID, CONCAT(BC.Codigo, ' | ', CONCAT_WS(' ',F.first_name,F.first_surname), ' | ', DATE_FORMAT(BC.created_at,'%d/%m/%Y %H:%i:%s')) AS Title FROM Borrador_Contabilidad BC INNER JOIN people F ON F.id = BC.Identificacion_Funcionario WHERE Estado = 'Activa' AND Tipo_Comprobante = '$tipo_comprobante' AND BC.Identificacion_Funcionario = $funcionario";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $oCon->setTipo('Multiple');
            $resultado = $oCon->getData();
            unset($oCon);
        }

        return json_encode($resultado);
    }

    public function detalles()
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $resultado = [];

        if ($id) {
            $query = "SELECT Id_Borrador_Contabilidad AS ID, Codigo, Datos FROM Borrador_Contabilidad WHERE Id_Borrador_Contabilidad = $id";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $resultado = $oCon->getData();
            unset($oCon);
        }

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BorradorContabilidad  $borradorContabilidad
     * @return \Illuminate\Http\Response
     */
    public function show(BorradorContabilidad $borradorContabilidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BorradorContabilidad  $borradorContabilidad
     * @return \Illuminate\Http\Response
     */
    public function edit(BorradorContabilidad $borradorContabilidad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BorradorContabilidad  $borradorContabilidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BorradorContabilidad $borradorContabilidad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BorradorContabilidad  $borradorContabilidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(BorradorContabilidad $borradorContabilidad)
    {
        //
    }
}
