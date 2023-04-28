<?php

namespace App\Http\Controllers;

use App\Models\Remision;
use Illuminate\Http\Request;
use App\Http\Services\consulta;

class RemisionController extends Controller
{


    public function datosIniciales()
    {
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        $query = 'SELECT B.Nombre AS label, CONCAT("B-",B.Id_Bodega_Nuevo) as value
            FROM Funcionario_Bodega_Nuevo FB
            INNER JOIN Bodega_Nuevo B
            ON FB.Id_Bodega_Nuevo=B.Id_Bodega_Nuevo
            WHERE FB.Identificacion_Funcionario=' . $id;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $bodega = $oCon->getData();
        unset($oCon);

        $query2 = 'SELECT  CONCAT("P-",PD.Id_Punto_Dispensacion) AS value, PD.Nombre AS label
            FROM Funcionario_Punto FP
            INNER JOIN  Punto_Dispensacion PD
            ON FP.Id_Punto_Dispensacion=PD.Id_Punto_Dispensacion
            WHERE FP.Identificacion_Funcionario=' . $id;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query2);
        $punto = $oCon->getData();
        unset($oCon);

        $query3 = 'SELECT LG.Nombre, CONCAT("L-",LG.Id_Lista_Ganancia) as Id_Lista_Ganancia
            FROM Lista_Ganancia LG';
        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query3);
        $lganancia = $oCon->getData();
        unset($oCon);

        $query = 'SELECT  CONCAT("C-",C.Id_Cliente) AS value, C.Id_Lista_Ganancia,  CONCAT(C.Nombre," - ",C.Id_Cliente) AS label
            FROM Cliente C WHERE C.Estado = "Activo"';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $clientes = $oCon->getData();
        unset($oCon);
        $resultado["Bodega"] = $bodega;
        $resultado["Punto"] = $punto;
        $resultado["Lista"] = $lganancia;
        $resultado["Clientes"] = $clientes;
        return response()->json($resultado);
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
     * @param  \App\Models\Remision  $remision
     * @return \Illuminate\Http\Response
     */
    public function show(Remision $remision)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Remision  $remision
     * @return \Illuminate\Http\Response
     */
    public function edit(Remision $remision)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Remision  $remision
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Remision $remision)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Remision  $remision
     * @return \Illuminate\Http\Response
     */
    public function destroy(Remision $remision)
    {
        //
    }
}
