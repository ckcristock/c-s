<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Person;
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

        $contratosAFinalizarSinDefinir = DB::table($contratosYNotificaciones,"contracts")
        ->select("contracts.*",DB::raw("null as renewed"))
        ->whereNotIn("contracts.contract_id", function ($query) {
            $query->select("contract_id")->from(with(new WorkContractFinishConditions)->getTable())->get();
        });

        $contratosAFinalizar = DB::table($contratosYNotificaciones,"contracts")
        ->select("contracts.*","wc.renewed")
        ->join('work_contract_finish_conditions as wc', 'contracts.contract_id', '=', 'wc.contract_id')
        ->union($contratosAFinalizarSinDefinir);

        $contratosAFinalizar = DB::table($contratosAFinalizar)
        ->whereNotNull('date_end')->orderBy('date_end', 'Desc')
        ->whereIn('dayDiff', [30, 0])->get();

        // Se procede a registrar la notificación si no se ha hecho ya.
        $contratosAFinalizar->each(function ($contrato) {
            $person = Person::fullName()
            ->where('id', $contrato->persona )->first();
            if ($contrato->dayDiff == 30) {
                if ($contrato->renewed == null) {
                    Alert::create([
                        'person_id' => 1,
                        'user_id' => $contrato->persona,
                        'modal' => 0,
                        'icon' => 'fas fa-file-contract',
                        'type' => 'Finalización de contrato',
                        'description' => 'Se le informa que su contrato finalizará el día '.$contrato->date_end
                    ]);
                    Alert::create([
                        'person_id' => 1,
                        'user_id' => 1,
                        'modal' => 0,
                        'icon' => 'fas fa-file-contract',
                        'type' => 'Notificación de finalización de contrato',
                        'description' => 'El funcionario '.$person->full_names.' con el contrato número CON'.$contrato->contract_id.' fue notificado
                        de su finalización para el día '.$contrato->date_end
                    ]);
                    WorkContractFinishConditions::create([
                        'person_id' => $contrato->persona,
                        'contract_id' => $contrato->contract_id,
                        'renewed' => 0
                    ]);
                }
            } else { // Si dayDiff == 0 (Último día del contrato)
                if ($contrato->renewed == 0) { // Preliquidado?
                    Person::where('id', $contrato->persona)
                    ->update(["status" => "PreLiquidado"]);
                }else{ // Renovado
                    $condicionesContratoARenovar = WorkContractFinishConditions::where('contract_id', $contrato->contract_id)
                    ->first();
                    unset($condicionesContratoARenovar->id);
                    unset($condicionesContratoARenovar->contract_id);
                    unset($condicionesContratoARenovar->created_at);
                    unset($condicionesContratoARenovar->updated_at);
                    WorkContract::create($condicionesContratoARenovar->toArray());
                }
                Alert::create([
                    'person_id' => 1,
                    'user_id' => 1,
                    'modal' => 0,
                    'icon' => 'fas fa-file-contract',
                    'type' => 'Preliquidación de funcionario',
                    'url' => ($contrato->renewed == 0)?'/rrhh/liquidados':'/ajustes/informacion-base/funcionario/'.$contrato->persona,
                    'description' => ($contrato->renewed == 0)?'El funcionario '.$person->full_names.' con el contrato número CON'.$contrato->contract_id.' fue preliquidado
                    y su contrato finalizado.':'El contrato número CON'.$contrato->contract_id.' con el funcionario '.$person->full_names.' fue renovado.'
                ]);
                WorkContract::where('id', $contrato->contract_id)
                ->update(["liquidated" => 1]);
                WorkContractFinishConditions::where('contract_id', $contrato->contract_id)
                ->delete();
            }
        });
    }
}
