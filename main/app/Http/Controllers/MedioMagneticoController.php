<?php

namespace App\Http\Controllers;

use App\Models\MedioMagnetico;
use Illuminate\Http\Request;
use App\Http\Services\consulta;

class MedioMagneticoController extends Controller
{

    public function lista()
    {
        $condicion = $this->strConditions();

        $query = "SELECT M.Id_Medio_Magnetico AS Id, M.Periodo, M.Codigo_Formato, M.Nombre_Formato, M.Tipo_Exportacion, M.Tipo_Columna, c.name as Empresa FROM Medio_Magnetico as M
        LEFT JOIN companies as c ON c.id = M.Id_Empresa
        WHERE Estado = 'Activo' $condicion";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $lista = $oCon->getData();
        unset($oCon);

        return json_encode($lista);
    }

    function strConditions()
    {
        $condicion = '';

        if (isset($_REQUEST['Tipo']) && $_REQUEST['Tipo'] != '') {
            $condicion .= " AND Tipo_Medio_Magnetico = 'Especial'";
        } else {
            $condicion .= " AND Tipo_Medio_Magnetico = 'Basico'";
        }

        return $condicion;
    }

    public function detalles()
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

        $detalles = [];

        if ($id) {
            $query = "SELECT Id_Medio_Magnetico, Periodo, Codigo_Formato, Nombre_Formato, Tipo_Exportacion, Detalles, Tipos, Tipo_Medio_Magnetico, Tipo_Columna, Columna_Principal FROM Medio_Magnetico WHERE Estado = 'Activo' AND Id_Medio_Magnetico = $id";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $resultado = $oCon->getData();
            unset($oCon);

            $detalles['encabezado'] = $resultado;
            $detalles['cuentas'] = $resultado['Detalles'];
            $detalles['tipos'] = $resultado['Tipos'];
        }

        return json_encode($detalles);
    }

    public function formatosEspeciales()
    {
        $query = "SELECT Id_Medio_Magnetico AS value, CONCAT(Codigo_Formato,' - ',Nombre_Formato) AS label FROM Medio_Magnetico WHERE Estado = 'Activo' AND Tipo_Medio_Magnetico = 'Especial'";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $lista = $oCon->getData();
        unset($oCon);

        return json_encode($lista);
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
     * @param  \App\Models\MedioMagnetico  $medioMagnetico
     * @return \Illuminate\Http\Response
     */
    public function show(MedioMagnetico $medioMagnetico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MedioMagnetico  $medioMagnetico
     * @return \Illuminate\Http\Response
     */
    public function edit(MedioMagnetico $medioMagnetico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedioMagnetico  $medioMagnetico
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedioMagnetico $medioMagnetico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedioMagnetico  $medioMagnetico
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedioMagnetico $medioMagnetico)
    {
        //
    }
}
