<?php

use App\Models\Alert;
use App\Models\WorkContract;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
    /* $contratos = WorkContract::
    whereNotNull('date_end')->orderBy('date_end', 'Desc')
    ->join('alerts', 'work_contracts.person_id', '!=', 'alerts.user_id')
    ->get(["work_contracts.person_id as persona", "liquidated", "work_contract_type_id", "date_end",
        DB::raw("date_format(alerts.created_at,'%Y-%m-%d') as fecha_notif"),
        DB::raw("datediff(date_end,curdate()) as dayDiff, datediff(date_end,alerts.created_at) as alertDiff")
    ]); */

    $contratosNotificados = WorkContract::join('alerts', 'work_contracts.person_id', '=', 'alerts.user_id')
    ->get(["w.person_id as persona", "date_end",
	DB::raw("datediff(date_end,curdate()) as dayDiff, date_format(alerts.created_at,'%Y-%m-%d') as fecha_notif,
	 CONCAT('Se renovará el próximo ',date_end) as Estado")
    ]);

    $contratosYNotificaciones = WorkContract::
    whereNotIn("persona",Alert::get("user_id"))->union($contratosNotificados)
    ->get(["w.person_id as persona", "date_end",
	DB::raw("datediff(date_end,curdate()) as dayDiff, NULL as fecha_notif,
    case
        when datediff(date_end,CURDATE()) BETWEEN 30 AND 45 then concat('Debe notificarse antes del ', adddate(date_end, -30))
        when datediff(date_end,CURDATE()) < 30 then CONCAT('Se renovará automáticamente el próximo ',date_end)
        ELSE 'Aún no requiere notificación'
    end as Estado")
    ]);

    $contratos = DB::table($contratosYNotificaciones)->whereNotNull('date_end')->orderBy('date_end', 'Desc')
    ->whereRaw("datediff(date_end,curdate()) > 0")
    ->get();



    //$hoy=Carbon::now();
    foreach($contratos as $contrato){
        //$fecha_notificacion = Carbon::parse($contrato->fecha_notif);
        //$fecha_salida = Carbon::parse($contrato->date_end);
        //$contrato['dayDiff'] = $hoy->diffInDays($fecha_salida,false);
        //$contrato['alertDiff'] = $fecha_notificacion->diffInDays($fecha_salida,false);
        if($contrato['alertDiff'] < 30){
            if($contrato['dayDiff'] < 30){
                $contrato['contractState'] = "Renovado";
            }elseif($contrato['dayDiff'] < 45){
                $contrato['contractState'] = "Notificación de renovación o terminación";
            }
        }
    }
    echo "{\"prueba\":".$contratos->whereBetween('dayDiff', [0, 45])."}";
})->purpose('Display a list of users');
