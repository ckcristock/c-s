<?php

use App\Models\WorkContract;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('listarUsers', function () {
    $contratos = WorkContract::
    whereNotNull('date_end')
    ->get(["person_id as persona", "liquidated", "work_contract_type_id", "date_end"]);

    $hoy=Carbon::now();
    foreach($contratos as $contrato){
        $fecha_salida = Carbon::parse($contrato->date_end);
        $contrato['dayDiff'] = $hoy->diffInDays($fecha_salida,false);
    }
    echo "{\"prueba\":".$contratos->where('dayDiff','>',30)->where('dayDiff','<=',45)."}";
})->purpose('Display a list of users');
