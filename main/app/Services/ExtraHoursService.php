<?php

namespace App\Services;

use App\Clases\stdObject;
use App\Models\Company;
use App\Models\DiarioTurnoRotativo;
use App\Models\Person;
use App\Models\RotatingTurn;
use App\Services\TranslateService;
use App\Traits\CalculateRotativoExtras;
use App\Traits\CalculateRotativoRecargos;
use App\Traits\Festivos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtraHoursService
{
    use Festivos;
    use CalculateRotativoExtras;
    use CalculateRotativoRecargos;
    public  $tiempoParaExtras;


    /**Funciones de estadisticas */
    public function getLates($dates)
    {
        return  DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                        join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->selectRaw('count(*) as total')
            ->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(l.real_entry) - TIME_TO_SEC(l.entry))) AS time_diff_total')
            ->first();
    }

    public function getAllByDependecies($dates)
    {
        return DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->join('dependencies as d', 'd.id', '=', 'ps.dependency_id')
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->selectRaw('count(*) as total, d.name')
            ->groupBy('d.id')
            ->get();
    }

    /**Arrivals */

    public function getPeople($id, $turn, $company_id, $dates)
    {
        $query = DB::table('people as p')
            ->join('work_contracts as w', function ($join) use ($dates) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                join people as u2 on u2.id = a2.person_id
                join work_contract_types as wt on a2. work_contract_type_id = wt.id
                WHERE date(a2.date_of_admission) <= "' . $dates[1] . '"
                    and(
                        date(a2.date_end) >= "' . $dates[0] . '"  or  a2.date_end is null
                    )
                group by u2.id

                )');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->where('ps.dependency_id', $id)
            ->where('w.company_id', $company_id)
            ->where('w.turn_type', $turn)
            ->where('p.status', '!=', 'liquidado')
            ->when(Request()->get('person_id'), function ($q, $fill) {
                $q->where('p.id', $fill);
            });
        if ($turn == 'rotativo') {
            $query->whereExists(function ($query) use ($dates) {
                $query->select(DB::raw(1))
                    ->from('rotating_turn_hours as la')
                    ->whereColumn('la.person_id', 'p.id')
                    ->whereBetween(DB::raw('DATE(la.date)'), $dates);
            });
        }

        return $query->select('p.first_name', 'p.first_surname', 'p.id', 'p.image', 'w.turn_type')
            ->get();
    }

    public function funcionarioRotativo($filtroDiarioFecha)
    {
        $translateService = new TranslateService();
        /* return Person::with('diariosTurnoRotativo')->get(); */
        $funcionario =  Person::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
            ->with(['horariosTurnoRotativo' => $filtroDiarioFecha])
            ->find(request()->get('id'))->toArray();
        if (isset($funcionario['horarios_turno_rotativo'])) {

            $funcionario['daysWork'] = $funcionario['horarios_turno_rotativo'];
            foreach ($funcionario['diarios_turno_rotativo']  as  $diario) {
                foreach ($funcionario['horarios_turno_rotativo'] as $ky => $turno) {

                    if ($turno['date'] == $diario['date']) {
                        $diario['nombreDia'] = $translateService->translateDay(Carbon::create($diario['date'])->englishDayOfWeek);
                        $diario['turnoOficial'] = RotatingTurn::find($turno['rotating_turn_id'])->toArray();
                        array_push($funcionario['daysWork'][$ky], ['day' => $diario]);
                    }
                }
            }
            unset($funcionario['diarios_turno_rotativo']);
        } else {
            return false;
        }
        return $funcionario;
    }

    public function calcularExtras($funcionario)
    {
        //Elimino la cosulta para evitar incrementar el tiempo de respuesta
        //Esto es el máximo de la empresa semanal
        //$tiempoAsignado = Company::first()->toArray()['work_time'];
        $tiempoAsignado = $funcionario['company_worked']['work_time'];

        $listaExtras = Array();

        /**
         * Evaluar si el horario permite horas extras
         * sino, no calcular
         * Se toma en cuenta el horario para saber si es hora extra o recargo
         * aunque llegue temprano, no se toma como extra hasta que cumpla
         * las horas estipuladas para el turno
         */

         //dd(gettype($funcionario));
        foreach ($funcionario['days_work'] as $ky => $day) {
            //return $day;  //$day tiene: el horario y lo que realmenete cumplió en $day[0]['day']
            if (isset($day)) {
                //dd($day[0]['day']);//diario
                //Seteo valores
                //dd($funcionario['turno_fijo']);
                $turno = Request()->get('tipo');
                //$fecha = Carbon::create($day[0]['day']['date']);

                //requiero la fecha, porque sino toma la de hoy //dy

                $diario = isset($day[0]) ? $day[0]['day'] : null;
                //dd(isset($day[0]));

                if (!is_null($diario)){
                    $dayStart = Carbon::create($diario['date'].' '.$funcionario['company_worked']['night_end_time']);
                    $dayEnd = Carbon::create($diario['date'].' '.$funcionario['company_worked']['night_start_time']);
                } else {
                    isset($day[0]);
                    break;
                }
                //dd($day[0]['day']['date']);

                //dd($day[0]['day']['date'].' '.$funcionario['company_worked']['night_end_time']);
                /**
                 * sino es fijo, es rotativo, entonces le asigno la tolerancia del rotativo
                 * (06-12-22) aún no evalúo el rotativo, pendiente
                 *
                 */
                $toleranciaSalida = ($turno == 'Fijo') ? $funcionario['turno_fijo']['leave_tolerance'] : 0;
                $toleranciaEntrada = ($turno == 'Fijo') ? $funcionario['turno_fijo']['entry_tolerance'] : 0;
                $horasExtras = $funcionario['turno_fijo']['extra_hours'];

                if ($turno == 'Fijo' && $horasExtras == true) {
                    $diferenciasDia = $this->diferenciaHorariosFijo($day, $toleranciaEntrada, $toleranciaSalida, $dayStart, $dayEnd);
                    array_push($listaExtras, $diferenciasDia);
                    //dd($diferenciasDia);
                    //calculo horas extras
                } else {
                    //Este turno no posee Horas Extras
                }

                //dd('por aca voy -  realmente');
                //OJO, debe retornar $funcionario



                //dd($toleranciaSalida);
                //$laborado = $day[0]['day'];
                //$laborado = $day['day']; //Lunes, Martes, etc

                /*  $extras = new stdObject();
                $extras->HorasExtrasNocturnas = 0;
                $extras->HorasExtrasDiurnas = 0;
                $extras->HorasExtrasNocturnasDominicales = 0; //sobra
                $extras->HorasExtrasDiurnasDominicales = 0; //sobra
                $extras->horasExtrasDominicalesFestivasNocturnas = 0;
                $extras->horasExtrasDominicalesFestivasDiurnas = 0;


                $recargos = new stdObject();
                $recargos->horasRecargoDominicalNocturna = 0;
                $recargos->horasRecargoNocturna = 0;
                $recargos->horasRecargoFestivo = 0;//sobra
                $recargos->horasRecargoDominicalDiurno = 0;
 */
                //dd($day[0]['day']);








                //Asistencia

                /* $leaveTime = $turno === 'Fijo' ? 'leave_time_two' : 'leave_time_one';
                //dd($funcionario['turno_fijo']['horarios_turno_fijo'][$ky]);
                $turnoFijo = $funcionario['turno_fijo'] ?? 'empty';
                //dd($turnoFijo);
                //dd(strtoupper($laborado));
                //dd($funcionario['fecha_inicio']->isDayOfWeek());
                //return $funcionario;
                //return($this->Asistencia("12/05/2022", "08:30", "18:25"));



                $entrada = (Carbon::create($day[0]['day']['entry_time_one']))->betweenIncluded($dayStart, $dayEnd) ? 'diurna' : 'nocturna';
                $salida = (Carbon::create($day[0]['day']['leave_time_two']))->betweenIncluded($dayStart, $dayEnd) ? 'diurna' : 'nocturna';

                $jornada = '';

                if ($entrada == 'diurna' && $salida == 'diurna') {
                    $jornada = 'diurna';
                } elseif ($entrada == 'nocturna' && $salida == 'nocturna') {
                    $jornada = 'nocturna';
                } else {
                    $jornada = 'mixta';
                }

 */
                //dd($nightEnd);
                //dd(Carbon::create($day[0]['day']['entry_time_one']));
                /* dd(Carbon::create($day[0]['day']['leave_time_two']));
                dd((Carbon::create($day[0]['day']['leave_time_two']))->betweenIncluded($nightEnd, $nightStart));
                dd((Carbon::create($day[0]['day']['entry_time_one']))->betweenIncluded($nightEnd, $nightStart));
                //dd($funcionario['company_worked']);
                $diferenciaEntrada = Carbon::create($day['entry_time_one'])->diff(Carbon::create($day[0]['day']['entry_time_one']));
                dd($diferenciaEntrada->format('%H horas %i minutos %s segundos')); */

              /*   if ($turnoFijo != 'empty') {
                    //calcular Asistencia  ??
                    //dd('por aca voy');
                } else {
                    return 'es rotativo';
                }
 */
                //[$asistencia, $salida, $fechaSalida] = $this->Asistencia($laborado['date'], $laborado['entry_time_one'], $laborado[$leaveTime]);

                #dd($laborado['entry_time_one']);
                //Tiempo Laborado
                /* $tiempoLaborado = 0;
                ##!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! aca mejorar con los horarios
                $tiempoLaborado = $this->workedTime($laborado, $asistencia, $salida);

                ##!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                /*    if($day['id'] == 10){
                            dd($laborado);
                        } */

                /*  $fechaSalida = $this->getLeaveDate($laborado); */
                //dd($fechaSalida);
                /* $FinTurnoOficcial =  $asistencia->copy()->addMinutes($tiempoAsignado);
                $FinTurnoReal =  $asistencia->copy()->addMinutes($tiempoLaborado);


                //Se arma el objecto que sera enviado como parametro
                $argumentos = new stdObject();
                $argumentos->tiempoAsignado = $tiempoAsignado;
                $argumentos->toleranciaSalida = $toleranciaSalida;
                $argumentos->tiempoLaborado = $tiempoLaborado;
                $argumentos->FinTurnoOficcial = $FinTurnoOficcial;
                $argumentos->FinTurnoReal = $FinTurnoReal;
                $argumentos->fechaSalida = $fechaSalida;
                $argumentos->asistencia = $asistencia;

                //Compruebo si trabajo mas del tiempo fijado y la tolerancia (Si trabajó extras)
                /*    dd($tiempoLaborado); */
                /* if ($tiempoLaborado > ($tiempoAsignado + $toleranciaSalida)) {
                    $recargos = $this->getDataRecargo($argumentos);
                    $extras = $this->getDataExtra($argumentos);
                }

                if ($tiempoLaborado <= ($tiempoAsignado + $toleranciaSalida)) {
                    $recargos = $this->getDataRecargoSinExtras($argumentos);
                }

                $funcionario['daysWork'][$ky]['tiempoLaborado'] = roundInHalf($tiempoLaborado / 60);
                $funcionario['daysWork'][$ky]['tiempoAsignado'] = roundInHalf($tiempoAsignado / 60);
                $funcionario['daysWork'][$ky]['horasRecargoDominicalNocturna'] = roundInHalf($recargos->horasRecargoDominicalNocturna / 60);
                //se comenta por que no funciona
                //$funcionario['daysWork'][$ky]['horasRecargoNocturna'] = roundInHalf($recargos->horasRecargoNocturna / 60);
                $funcionario['daysWork'][$ky]['horasRecargoNocturna'] = 0;
                $funcionario['daysWork'][$ky]['horasRecargoFestivo'] = roundInHalf($recargos->horasRecargoFestivo / 60);

                $funcionario['daysWork'][$ky]['horasRecargoDominicalDiurno'] = roundInHalf($recargos->horasRecargoDominicalDiurno / 60);
                $funcionario['daysWork'][$ky]['HorasExtrasNocturnas'] = roundInHalf($extras->HorasExtrasNocturnas / 60);
                $funcionario['daysWork'][$ky]['HorasExtrasDiurnas'] = roundInHalf($extras->HorasExtrasDiurnas / 60);
                $funcionario['daysWork'][$ky]['HorasExtrasNocturnasDominicales'] = roundInHalf($extras->HorasExtrasNocturnasDominicales / 60);
                $funcionario['daysWork'][$ky]['HorasExtrasDiurnasDominicales'] = roundInHalf($extras->HorasExtrasDiurnasDominicales / 60);

                $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasNocturnas'] = roundInHalf($extras->horasExtrasDominicalesFestivasNocturnas / 60);
                $funcionario['daysWork'][$ky]['horasExtrasDominicalesFestivasDiurnas'] = roundInHalf($extras->horasExtrasDominicalesFestivasDiurnas / 60);
            */ } else {

                // No trabajó

            }
        }
        $funcionario['extras'] = $listaExtras;
        return $funcionario;
    }

    /**
     * función para calcular la direfencia entre los horarios y las horas reales
     * recibe el horario en arreglo por día y el diario de horas reales,
     * devuelve un arreglo por día con su Hora Extra u Hora Recargo
     * //primero se está considerando solo los fijos
     */
    public function diferenciaHorariosFijo($horario, $toleranciaEntrada, $toleranciaSalida, $comienzoDia, $finDia)
    {
        $diario = $horario[0]['day'];

        //horario mañana
        $esperadoIn = Carbon::create($diario['date'] . $horario['entry_time_one']);
        $realIn = Carbon::create($diario['date'] . $diario['entry_time_one']);

        $esperadoOut = Carbon::create($diario['date'] . $horario['leave_time_one']);
        $realOut = Carbon::create($diario['date'] . $diario['leave_time_one']);
/*
        // Diferencia entre la hora de entrada y la real
        // horas extra antes del horario de trabajo
        $entradaUno = $esperadoIn->diff($realIn);
        //diferencia entre el horario de lunch y hora real
        $salidaUno = $esperadoOut->diff($realOut);
        //$intEntradaUno = $this->redondearHora($entradaUno, $toleranciaEntrada); */

        $cantHorasHorario = new stdObject([
            'horas'=> intval($esperadoIn->diff($esperadoOut)->format('%H')),
            'minutos'=> intval($esperadoIn->diff($esperadoOut)->format('%i')),
            'segundos'=> intval($esperadoIn->diff($esperadoOut)->format('%s'))
        ]);

        $cantHorasDiario = new stdObject([  //horas trabajadas
            'horas'=> intval($realIn->diff($realOut)->format('%H')),
            'minutos'=> intval($realIn->diff($realOut)->format('%i')),
            'segundos'=> intval($realIn->diff($realOut)->format('%s'))
        ]);

        ////horario tarde
        $esperadoInDos = Carbon::create($diario['date'] . $horario['entry_time_two']);
        $realInDos = Carbon::create($diario['date'] . $diario['entry_time_two']);

        $esperadoOutDos = Carbon::create($diario['date'] . $horario['leave_time_two']);
        $realOutDos = Carbon::create($diario['date'] . $diario['leave_time_two']);

       /*  $entradaDos = $esperadoInDos->diff($realInDos); // diferencia entre el horario de lunch y hora real
        $salidaDos = $esperadoOutDos->diff($realOutDos); // horas extras a la hora
        //$intSalidaDos = $this->redondearHora($salidaDos, $toleranciaSalida); */

        $cantHorasHorario->horas += intval($esperadoInDos->diff($esperadoOutDos)->format('%H'));
        $cantHorasHorario->minutos += intval($esperadoInDos->diff($esperadoOutDos)->format('%i'));
        $cantHorasHorario->segundos += intval($esperadoInDos->diff($esperadoOutDos)->format('%s'));

        $cantHorasDiario->horas += intval($realInDos->diff($realOutDos)->format('%H'));
        $cantHorasDiario->minutos += intval($realInDos->diff($realOutDos)->format('%i'));
        $cantHorasDiario->segundos += intval($realInDos->diff($realOutDos)->format('%s'));


        $diferenciaReal = new stdObject([
            'horas'=> $cantHorasDiario->horas - $cantHorasHorario->horas,
            'minutos'=> $cantHorasDiario->minutos - $cantHorasHorario->minutos,
            'segundos'=> $cantHorasDiario->segundos - $cantHorasHorario->segundos
        ]);
        //dd($diferenciaReal);
        $intSalida = $this->redondearHora($diferenciaReal, $toleranciaSalida);
        //$arrayUno = $this->tipoHora($diario['date'], $realOutDos, $comienzoDia, $finDia);
/*         [$domingoUno, $festivoUno, $diurnoUno] = $this->tipoHora($diario['date'], $realOutDos, $comienzoDia, $finDia);
        [$domingoDos, $festivoDos, $diurnoDos] = $this->tipoHora($diario['date'], $realOutDos->subHours($intSalida), $comienzoDia, $finDia); */

        $horasExtras = new stdObject([
            "ht" => $cantHorasDiario->horas,
            "hed" => 0,
            "hen" => 0,
            "heddf" => 0,
            "hendf" => 0,
            "hrndf" => 0,
            "hrddf" => 0,
        ]);

        for($i = 1; $i<= $intSalida; $i++ ) {
            $array = $this->tipoHora($diario['date'], $realOutDos->subHours($intSalida-1), $comienzoDia, $finDia);
            if ($intSalida > 0) {
                array_push($array, true);
            } else {
                array_push($array, false);
            }


            //dd($array);
            if (($array <=> [false, false, true, true])==0) {    //H E D
                $horasExtras->hed++;
            } elseif (($array <=> [false, false, false, true])==0) {  //H E N
                $horasExtras->hen++;
            } elseif (($array <=> [true, true, true, true])==0) {  //H E D DF
                $horasExtras->heddf++;
            } elseif (($array <=> [true, true, false, true])==0) {  //H E N DF
                $horasExtras->hendf++;
            } elseif (($array <=> [true, true, false, false])==0) {  //H R N DF
                $horasExtras->hrndf++;
            } elseif (($array <=> [false, false, false, false])==0) {  //H R N
                $horasExtras->hrn++;
            } elseif (($array <=> [true, true, true, false])==0) {  //H R D DF
                $horasExtras->hrddf++;
            }



        }
        //dd($horasExtras);





        /* if( $extra ) { //extras

        } else { // recarga
            if ($diurnoDos){
                if ( $domingoDos || $festivoDos){
                    //DF
                }
                else{

                }
            } else { /// nocturno

            }
        } */

        /* $jornada = '';
        if ($diurnoUno && $diurnoDos) {
            $jornada = 'diurna';

        } elseif (!$diurnoUno && !$diurnoDos) {
            $jornada = 'nocturna';
        } else {
            $jornada = 'mixta';
        }

        dd($jornada);

        dd($diurnoUno);
        if ($intSalida > 0) {
            array_push($array, true);
        } else {
            array_push($array, true);
        }

        //dd($intSalida);
        dd($array); */

        $diferencias = new stdObject([
            'day' => $horario['day'],
            'date' => $diario['date'],
            'schedule_in' => $esperadoIn->toDateTimeString(),
            'hour_in' => $realIn->toDateTimeString(),
            'schedule_out' => $esperadoOutDos->toDateTimeString(),
            'hour_out' => $realOutDos->toDateTimeString(),
            /* 'dif_in_one' => [
                'hours' => ($entradaUno->format('%H')),
                'minutes' => ($entradaUno->format('%i')),
                'seconds' => ($entradaUno->format('%s'))
            ],
            'dif_out_one' => [
                'hours' => ($salidaUno->format('%H')),
                'minutes' => ($salidaUno->format('%i')),
                'seconds' => ($salidaUno->format('%s'))
            ],
            //'extras_in' => $intEntradaUno,
            'dif_in_two' => [
                'hours' => ($entradaDos->format('%H')),
                'minutes' => ($entradaDos->format('%i')),
                'seconds' => ($entradaDos->format('%s'))
            ],
            'dif_out_two' => [
                'hours' => ($salidaDos->format('%H')),
                'minutes' => ($salidaDos->format('%i')),
                'seconds' => ($salidaDos->format('%s'))
            ], */
            'hours_schedule' => $cantHorasHorario,
            'extras' => $intSalida,
            'classification' => $horasExtras
        ]);
        //dd($diferencias);
        return $diferencias;
    }

    /**
     * función para clasificar el tipo de hora
     * recibe la fecha, hora inicio jornada diurna, hora fin jornada diurna
     * devuelve un arreglo  boolean con la clasificación de hora [siEsDomingo, siEsFeriado, siEsDiurno ]
     * la fecha debe ser del mismo día
     * @param $fecha //ejemplo "2022-12-11"
     * @param $hora  //ejemplo Carbon::create("2022-12-11 05:01:43")
     * @param $comienzoDia  //ejemplo "2022-12-11 06:01:00"
     * @param $finDia  //ejemplo  "2022-12-11 21:01:00"
     * Dy (07-12-2022 9:40am)
     */
    public function tipoHora($fecha, $hora, $comienzoDia, $finDia)
    {
        $fecha = Carbon::create($fecha);

        $domingo = $fecha->dayOfWeek == 0 ? true : false;

        //formato de festivos "YYY-mm-dd"
        $festivosAnio = $this->getFestivos();
        $festivo = false;
        foreach ($festivosAnio as $festive) {
            $festCarbon = Carbon::create($festive);
            if ($fecha == $festCarbon){
                $festivo = true;
            }
        }

        //Hora se recibe en formato Carbon::create("2022-12-11 05:01:43")
        //por ello no se convierte acá
        $diurno = false;
        if ($hora->betweenIncluded($comienzoDia, $finDia)) {
            $diurno = true;
        }

        return [$domingo, $festivo, $diurno];
    }

    /**
     * función para calcular de acuerdo a la tolerancia si es ua hora o no
     * @param $diferencia // tipo: array['horas'= int, 'minutos'=int, 'segundos'=int]
     * Dy
     */
    public function redondearHora($diferencia, $tolerancia)
    {
        //la tolerancia viene en minutos
        $horasExtra = 0;

        if ($tolerancia <> 0) {
            $horasExtra = $diferencia->horas;
            if ( $diferencia->minutos > $tolerancia ) {
                $horasExtra++;
            }
        }
        return $horasExtra;
    }

    public function diferenciaHorariosRotativo($horario, $toleranciaEntrada, $toleranciaSalida) {

    }




    //convierte strings a Carbon
    private function getLeaveDate($laborado)
    {
        $date = '';
        if ($laborado['leave_time_one']) {

            $date = $laborado['leave_date'] . $laborado['leave_time_one'];
        } elseif ($laborado['breack_time_one'] && !$laborado['breack_time_two']) {

            $date = $laborado['breack_one_date'] . $laborado['breack_time_one'];
        } elseif (isset($laborado['launch_time_one']) && $laborado['launch_time_one']) {

            $date = $laborado['launch_one_date'] . $laborado['launch_time_one'];
        }

        return  Carbon::parse($date);
    }

    /**
     * este parece útil, pero solo calcula uno a la vez
     */
    public function workedTime($laborado, $asistencia, $salida)
    {

        $diffTotal = 0;
        //si tiene hora 1 de launch
        if ($laborado['leave_time_one']) {
            $diffTotal += $asistencia->diffInMinutes($salida);
        } elseif ($laborado['breack_time_one'] && !$laborado['breack_time_two']) {

            $launchEntry = Carbon::parse($laborado['breack_one_date'] . $laborado['breack_time_one']);



            $diffTotal = $asistencia->diffInMinutes($launchEntry);
        } elseif (isset($laborado['launch_time_one']) && $laborado['launch_time_one']) {

            $launchEntry = Carbon::parse($laborado['launch_one_date'] . $laborado['launch_time_one']);

            $diffTotal = $asistencia->diffInMinutes($launchEntry);
        }

        //si tiene hora 2 de launch
        if (isset($laborado['launch_time_two']) && $laborado['launch_time_two']) {
            $launchEntry = Carbon::parse($laborado['launch_two_date'] . $laborado['launch_time_two']);
            $launchLe = Carbon::parse($laborado['breack_two_date'] . $laborado['breack_time_two']);
            if ($salida) {
                $diffTotal += $launchEntry->diffInMinutes($salida);
            }
        }


        if (isset($laborado['entry_time_two']) && Request()->get('tipo') == 'Fijo'  && $laborado['entry_time_two']) {
            $launchEntry = Carbon::parse($laborado['date'] . $laborado['entry_time_two']);
            $launchLe = Carbon::parse($laborado['date'] . $laborado['leave_time_one']);
            $diffTotal = $launchEntry->diffInMinutes($launchLe);
        }

        return $diffTotal;
    }

    //no le veo utiliddad a esta función
    public function Asistencia($fecha, $horaEntrada, $horaSalida)
    {

        $asistencia = Carbon::parse($fecha . $horaEntrada);
        $salida =  Carbon::parse($fecha . $horaSalida);
        $fechaSalida =  Carbon::parse($fecha .  $horaSalida);

        if (($horaSalida >=   '00:00:00' && $horaSalida <   $horaEntrada)) {
            $salida->addDay();
            $fechaSalida->addDay();
        }

        return [$asistencia, $salida,  $fechaSalida];
    }

    public function setTiempoExtra($cantidadEnMinutos)
    {
        $this->tiempoParaExtras - $cantidadEnMinutos;
    }

    public function prueba(Request $request)  //esto se borra
    {
        $fechaInicio = "2022-06-12";
        $fechaFin = "2022-06-18";

        $filtroDiarioFecha = function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('date', [$fechaInicio, $fechaFin])->orderBy('date');
        };
        $funcionario = $this->funcionarioFijo($filtroDiarioFecha, $fechaInicio, $fechaFin);

        /* $fecha = "2022-06-13";
        $hora = Carbon::create('2022-06-13 06:40:43');
        $diaIni = '2022-06-13 06:01:00';
        $diaFin = '2022-06-13 21:01:00'; */
        $fecha = $request->fecha;
        $hora = Carbon::create($request->hora);
        $diaIni = $request->diaIni;
        $diaFin = $request->diaFin;
        dd($this->tipoHora($fecha, $hora, $diaIni, $diaFin));

        return $this->calcularExtras($funcionario);
    }


    public function funcionarioFijo($filtroDiarioFecha, $fechaInicio, $fechaFin)
    {
        try {

            $translateService = new TranslateService();

            /*         $funcionario =  Person::with(['diariosTurnoRotativo' => $filtroDiarioFecha])
            ->with(['horariosTurnoRotativo' => $filtroDiarioFecha])
            ->find(request()->get('id'))->toArray(); */

            /*  $funcionario =  Person::with(
            ['diariosTurnoFijo' => $filtroDiarioFecha],
        )
            ->with('contractultimate', function ($q) {
                $q->select('*')
                    ->with('fixedTurn.horariosTurnoFijo');
            })
                ->find(request()->get('id')); */
            $funcionario = Person::with([
                'diariosTurnoFijo' => function ($q) use ($filtroDiarioFecha) {
                    $q->select('*')
                        ->where($filtroDiarioFecha);
                },
                'contractultimate' => function ($q) {
                    $q->select('*')
                        ->with('fixedTurn');
                }
            ])->with('companyWorked')
                ->find(11545)->toArray();
            $funcionario['fecha_inicio'] = Carbon::create($fechaInicio)->locale('es_ES');
            $funcionario['fecha_fin'] = Carbon::create($fechaFin)->locale('es_ES');

            if (isset($funcionario['contractultimate']['fixed_turn']['horarios_turno_fijo'])) {
                $funcionario['turno_fijo'] = $funcionario['contractultimate']['fixed_turn'];
                unset($funcionario['contractultimate']);

                $funcionario['days_work'] = $funcionario['turno_fijo']['horarios_turno_fijo'];
                foreach ($funcionario['diarios_turno_fijo']  as  $diario) {
                    foreach ($funcionario['turno_fijo']['horarios_turno_fijo'] as $ky => $turno) {
                        if ($turno['day'] == $translateService->translateDay(Carbon::create($diario['date'])->englishDayOfWeek)) {
                            array_push($funcionario['days_work'][$ky], ['day' => $diario]);
                        }
                    }
                }
                unset($funcionario['diarios_turno_fijo']);
            } else {
                return response()->json('Horario no asignado ');
            }

            return $funcionario;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
