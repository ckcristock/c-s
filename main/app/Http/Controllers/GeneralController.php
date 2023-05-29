<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Models\Alert;
use App\Models\ElectronicPayroll;
use App\Services\MarcationService;
use App\Services\PersonService;
use Carbon\Carbon;

include(app_path() . '/Http/Services/comprobantes/ObtenerProximoConsecutivo.php');

class GeneralController extends Controller
{

    public function pruebas()
    {
        Alert::create([
            'person_id' => 1,
            'user_id' => 1,
            'modal' => 0,
            'icon' => 'fas fa-file-contract',
            'type' => 'Finalización de contrato',
            'description' => 'Tu contrato finalizará el día '
        ]);
        //event(new NewNotification('hola2'));
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
