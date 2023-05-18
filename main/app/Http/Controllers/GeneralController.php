<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Models\ElectronicPayroll;
use App\Services\MarcationService;
use App\Services\PersonService;
use Carbon\Carbon;

include(app_path() . '/Http/Services/comprobantes/ObtenerProximoConsecutivo.php');

class GeneralController extends Controller
{

    public function pruebas()
    {
        $hoy = date('Y-m-d');
        $hactual = date("H:i:s");

        $dias = array(
            0 => "Domingo",
            1 => "Lunes",
            2 => "Martes",
            3 => "Miercoles",
            4 => "Jueves",
            5 => "Viernes",
            6 => "Sabado"
        );
        $candidato = 'e4e33943-2674-4ef1-b087-d766bc14409a';
        $ayer = date("Y-m-d", strtotime(date("Y-m-d") . ' - 1 day'));
        $funcionario = PersonService::funcionario_turno($candidato, $dias[date("w", strtotime($hoy))], $hoy, $ayer);
        $rotativo_hoy = $funcionario->diariosTurnoRotativoHoy[0];
        $durationLaunch = MarcationService::makeTime($hoy, $hactual, $rotativo_hoy->date, $rotativo_hoy->turnoRotativo->breack_time_two);
        dd($funcionario);
    }

    public function listaGenerales()
    {
        $mod = (isset($_REQUEST['modulo']) ? $_REQUEST['modulo'] : '');

        $condicion = '';

        if ($mod === 'Cliente') {
            $condicion = " WHERE Estado != 'Inactivo'";
        } elseif ($mod === 'Resolucion') {
            $condicion = "  ORDER BY Id_Resolucion DESC";
        }

        $query = 'SELECT * FROM ' . $mod . $condicion;
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $resultado = $oCon->getData();
        unset($oCon);
        return json_encode($resultado);
    }

    public function detalle()
    {
        $mod = (isset($_REQUEST['modulo']) ? $_REQUEST['modulo'] : '');
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        /*
        $oItem = new complex($mod,"Id_".$mod,$id);
        $detalle= $oItem->getData();
        unset($oItem);*/
        $query = 'SELECT D.*
        FROM ' . $mod . ' D
        WHERE D.Id_' . $mod . ' = ' . $id;
        $oCon = new consulta();
        $oCon->setQuery($query);
        $detalle = $oCon->getData();
        unset($oCon);
        //var_dump ($detalle);
        return json_encode($detalle, JSON_UNESCAPED_UNICODE);
    }

    public function getCodigo()
    {
        $tipo = $_REQUEST['Tipo'];

        $mes = isset($_REQUEST['Fecha']) && $_REQUEST['Fecha'] != '' ? date('m', strtotime($_REQUEST['Fecha'])) : date('m');
        $anio = isset($_REQUEST['Fecha']) && $_REQUEST['Fecha'] != '' ? date('Y', strtotime($_REQUEST['Fecha'])) : date('Y');

        $consecutivo = obtenerProximoConsecutivo($tipo, 1, $mes, $anio);

        return json_encode([
            "consecutivo" => $consecutivo
        ]);
    }
}
