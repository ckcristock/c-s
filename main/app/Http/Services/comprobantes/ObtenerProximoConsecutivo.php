<?php

use App\Http\Services\consulta;
use App\Http\Services\complex;

function obtenerProximoConsecutivo($tipo, $company, $mes = null, $anio = null, $dia = null)
{
    $mes = $mes === null ? date('m') : $mes;
    $anio = $anio === null ? date('m') : $anio;
    $dia = $dia === null ? date('d') : $dia;

    $query = "SELECT * FROM Comprobante_Consecutivo WHERE Tipo = '$tipo' AND company_id=$company";

    $oCon = new consulta();
    $oCon->setQuery($query);
    $resultado = $oCon->getData();
    unset($oCon);

    $prefijo = $resultado['Prefijo'];
    $consecutivo = $resultado['Consecutivo'];


    $consecutivo =  $prefijo . ($resultado['Anio'] == "SI" ? $anio : "") . ($resultado['Mes'] == "SI" ? $mes : "") . ($resultado['Dia'] == "SI" ? $dia : "") . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);

    return $consecutivo;
}

function generarConsecutivo($tipo, $mes = null, $anio = null)
{
    $mes = $mes === null ? date('m') : $mes;
    $anio = $anio === null ? date('Y') : $anio;

    if ($tipo == 'Cierre_Anio') {
        $mes = '';
        $query = "SELECT * FROM Comprobante_Consecutivo WHERE Tipo = '$tipo' ";/* AND Anio = '$anio' */
    } else {
        $query = "SELECT * FROM Comprobante_Consecutivo WHERE Tipo = '$tipo' "; /* AND Mes = '$mes' AND Anio = '$anio' */
    }


    $oCon = new consulta();
    $oCon->setQuery($query);
    $resultado = $oCon->getData();
    unset($oCon);

    $prefijo = $resultado['Prefijo'];
    $consecutivo = $resultado['Consecutivo'];

    $consecutivo =  $prefijo . $anio . $mes . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);

    # GENERAR NUEVO CONSECUTIVO PARA EL PROXIMO DOCUMENTO.

    $oItem = new complex('Comprobante_Consecutivo', 'Id_Comprobante_Consecutivo', $resultado['Id_Comprobante_Consecutivo']);
    $nuevo_consecutivo = $oItem->Consecutivo + 1;
    $oItem->Consecutivo = number_format($nuevo_consecutivo, 0, "", "");
    $oItem->save();
    unset($oItem);

    return $consecutivo;
}
