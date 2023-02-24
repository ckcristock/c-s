<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\consulta;

class GeneralController extends Controller
{
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

    public function detalle(){
        $mod = ( isset( $_REQUEST['modulo'] ) ? $_REQUEST['modulo'] : '' );
        $id = ( isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : '' );
        /*
        $oItem = new complex($mod,"Id_".$mod,$id);
        $detalle= $oItem->getData();
        unset($oItem);*/
        $query = 'SELECT D.*
        FROM '.$mod.' D
        WHERE D.Id_'.$mod.' = '.$id ;
        $oCon= new consulta();
        $oCon->setQuery($query);
        $detalle = $oCon->getData();
        unset($oCon);
        //var_dump ($detalle);
        return json_encode($detalle,JSON_UNESCAPED_UNICODE);
    }
}
