<?php

namespace App\Console\Commands;

use App\Models\DiarioTurnoFijo;
use App\Models\DiarioTurnoRotativo;
use App\Models\RotatingTurn;
use App\Models\RotatingTurnHour;
use App\Models\WorkContract;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndWorkShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:endworkshifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se agrega hora de salida final a los turnos del día que quedaron abiertos, se ejecuta cada día';

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
        $registrosFijosSinSalida = DiarioTurnoFijo::whereNull('leave_time_two')->get();
        $registrosRotativosSinSalida = DiarioTurnoRotativo::whereNull('leave_time_one')->get();
        foreach ($registrosRotativosSinSalida as $registro) {
            $personId = $registro->person_id;
            $fecha = $registro->date;

            $turno = RotatingTurnHour::where('person_id', $personId)
                ->where('date', $fecha)
                ->first();

            if ($turno) {
                $horaSalida = RotatingTurn::find($turno->rotating_turn_id)->leave_time;
                $horaEntrada = $registro->entry_time_one;
                $leaveDate = $horaSalida < $horaEntrada ? Carbon::parse($fecha)->addDay() : $fecha;
                $registro->leave_time_one = $horaSalida;
                $registro->leave_date = $leaveDate;
                $registro->save();
            }
        }
        foreach ($registrosFijosSinSalida as $registro) {
            $personId = $registro->person_id;
            $fecha = $registro->date;
            $fechaCarbon = Carbon::parse($fecha);
            $nombreDia = $fechaCarbon->locale('es')->isoFormat('dddd');
            $turno = WorkContract::where('person_id', $personId)->where('liquidated', 0)
                ->with(['fixedTurn.horariosTurnoFijo' => function ($query) use ($nombreDia) {
                    $query->where('day', '=', $nombreDia);
                }])
                ->first();

            if ($turno && $turno->fixedTurn) {
                $horaSalida = $turno->fixedTurn->horariosTurnoFijo->first()->leave_time_two;
                $horaSalidaAlmuerzo = $turno->fixedTurn->horariosTurnoFijo->first()->leave_time_one;
                $horaEntradaAlmuerzo = $turno->fixedTurn->horariosTurnoFijo->first()->entry_time_two;
                $registro->leave_time_two = $horaSalida;
                $registro->leave_time_one = $horaSalidaAlmuerzo;
                $registro->entry_time_two = $horaEntradaAlmuerzo;
                $registro->save();
            }
        }
    }
}
