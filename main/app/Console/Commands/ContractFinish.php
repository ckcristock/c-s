<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\WorkContract;
use App\Models\WorkContractFinishConditions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContractFinish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:finish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tratamiento de contratos próximos a finalizar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $contratosNotificados = WorkContract::select("work_contracts.id as contract_id",
            "work_contracts.person_id as persona",
            "date_end",
            DB::raw("datediff(date_end,curdate()) as dayDiff, date_format(alerts.created_at,'%Y-%m-%d') as fecha_notif")
        )->join('alerts', 'work_contracts.person_id', '=', 'alerts.user_id');

        $contratosYNotificaciones = WorkContract::select("work_contracts.id as contract_id",
            "work_contracts.person_id as persona",
            "date_end",
            DB::raw("datediff(date_end,curdate()) as dayDiff, NULL as fecha_notif")
        )
        ->whereNotIn("work_contracts.person_id", function ($query) {
            $query->select("user_id")->from(with(new Alert)->getTable())->get();
        })->union($contratosNotificados);

        $contratosAFinalizar = DB::table($contratosYNotificaciones)
            ->whereNotNull('date_end')->orderBy('date_end', 'Desc')
            ->whereBetween('dayDiff', [0, 45])->get();

        // Se procede a registrar la notificación si no se ha hecho ya.
        $contratosAFinalizar->each(function ($contrato) {
            if ($contrato->dayDiff == 30) {
                if ($contrato->fecha_notif == null) {
                    Alert::create([
                        'person_id' => 1,
                        'user_id' => $contrato->persona,
                        'modal' => 0,
                        'type' => 'Notificación',
                        'description' => 'Se le informa que su contrato finalizará el día ' . $contrato->date_end
                    ]);
                    WorkContractFinishConditions::create([
                        'person_id' => 1,
                        'contract_id' => $contrato->contract_id,
                        'renewed' => 0
                    ]);
                }
            }
        });

        echo $contratosAFinalizar;
    }
}
