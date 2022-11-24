<?php

use App\Models\Alert;
use App\Models\WorkContract;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

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

/* Artisan::command('listarUsers', function () {
    $contratosNotificados = WorkContract::select(
        "work_contracts.person_id as persona",
        "date_end",
        DB::raw("datediff(date_end,curdate()) as dayDiff, date_format(alerts.created_at,'%Y-%m-%d') as fecha_notif,
	 CONCAT('Se renovará el próximo ',date_end) as estado")
    )->join('alerts', 'work_contracts.person_id', '=', 'alerts.user_id');

    $contratosYNotificaciones = WorkContract::select(
        "work_contracts.person_id as persona",
        "date_end",
        DB::raw("datediff(date_end,curdate()) as dayDiff, NULL as fecha_notif,
    case
        when datediff(date_end,CURDATE()) BETWEEN 30 AND 45 then concat('Debe notificarse antes del ', adddate(date_end, -30))
        when datediff(date_end,CURDATE()) < 30 then CONCAT('Se renovará automáticamente el próximo ',date_end)
        ELSE 'Aún no requiere notificación' END as estado")
    )
        ->whereNotIn("work_contracts.person_id", function ($query) {
            $query->select("user_id")->from(with(new Alert)->getTable())->get();
        })->union($contratosNotificados);

    $contratos = DB::table($contratosYNotificaciones)
        ->whereNotNull('date_end')->orderBy('date_end', 'Desc')
        ->whereBetween('dayDiff', [0, 45])->get();

    // Se procede a registrar la notificación si no se ha hecho ya.
    $contratos->each(function ($contrato) {
        if ($contrato->dayDiff == 30) {
            if ($contrato->fecha_notif == null) {
                Alert::create([
                    'person_id' => 1,
                    'user_id' => $contrato->persona,
                    'modal' => 0,
                    'type' => 'Notificación',
                    'description' => 'Se le informa que su contrato será renovado el día ' . $contrato->date_end
                ]);
                $contrato->estado = "Se ha notificado la renovación del contrato al trabajador.";
            } else {
                $contrato->estado = "El trabajador ya había sido notificado.";
            }
        }
        if ($contrato->dayDiff == 0) {
            $contratoARenovar = WorkContract::
            select("*",DB::raw("ADDDATE(date_end,DATEDIFF(date_end,date_of_admission)) AS nueva_fecha_fin"))
            ->where("person_id",$contrato->persona)->orderBy("created_at","Desc")->first();
            if($contratoARenovar->old_date_end != null && Carbon::now()->diffInDays(Carbon::parse($contratoARenovar->old_date_end)) <= 0){
                $contrato->estado = "Este contrato ya fue renovado.";
            }else{
                $contratoARenovar->date_of_admission = Carbon::parse($contratoARenovar->date_end)->addDay()->format('Y-m-d');
                $contratoARenovar->old_date_end = $contratoARenovar->date_end;
                $contratoARenovar->date_end = $contratoARenovar->nueva_fecha_fin;
                unset($contratoARenovar->id);
                unset($contratoARenovar->created_at);
                unset($contratoARenovar->updated_at);
                unset($contratoARenovar->nueva_fecha_fin);
                WorkContract::create($contratoARenovar->toArray());
                $contrato->estado = "Se ha realizado la renovación del contrato al trabajador.";
            }
        }
    });

    echo "{\"prueba\": $contratos }";
})->purpose('Display a list of users'); */
