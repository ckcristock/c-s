<?php

use App\Models\ComprobanteConsecutivo;
use Carbon\Carbon;

if (!function_exists('sumConsecutive')) {
    function sumConsecutive($table)
    {
        $consecutivo = ComprobanteConsecutivo::where('table_name', $table)->first();
        $consecutivo->update(['Consecutivo' => $consecutivo->Consecutivo + 1]);

        return $consecutivo;
    }
}

if (!function_exists('getConsecutive')) {
    function getConsecutive($table)
    {
        $consecutivo = ComprobanteConsecutivo::where('table_name', $table)->first();
        return $consecutivo;
    }
}

if (!function_exists('generateConsecutive')) {
    function generateConsecutive($table, $city)
    {
        $consecutivo = ComprobanteConsecutivo::where('table_name', $table)->first();
        $today = Carbon::now();
        $today_ = new stdClass();
        $today_->anio = $today->format('y');
        $today_->mes = $today->format('m');
        $today_->dia = $today->format('d');
        $con = $consecutivo->Prefijo .
            ($consecutivo->city ? '.' . $city : '-') .
            str_pad($consecutivo->Consecutivo + 1, $consecutivo->longitud, 0, STR_PAD_LEFT) .
            ($consecutivo->Anio || $consecutivo->Mes || $consecutivo->Dia ? "-" : "") .
            ($consecutivo->Anio ? $today_->anio : "") .
            ($consecutivo->Mes ? $today_->mes : "") .
            ($consecutivo->Dia ? $today_->dia : "");
        return $con;
    }
}
