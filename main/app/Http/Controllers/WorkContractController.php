<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Person;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use NumberFormatter;

class WorkContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 20;
        return $this->success(
            DB::table('people as p')
                ->select(
                    DB::raw('concat("CON", w.id) as code'),
                    'p.id',
                    'p.identifier',
                    'p.first_name',
                    'p.first_surname',
                    'p.second_name',
                    'p.second_surname',
                    'p.image',
                    'p.status',
                    'posi.name as position',
                    'depe.name as dependency_name',
                    'gr.name as group_name',
                    'co.name as company_name',
                    'w.date_of_admission',
                    'w.date_end',
                    'wt.name as work_contract_type',
                    'wt.conclude'
                )
                ->when(Request()->get('person'), function ($q, $fill) {
                    $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
                })
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('work_contract_types as wt', 'wt.id', 'w.work_contract_type_id')
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as depe', function ($join) {
                    $join->on('depe.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'group_id');
                })
                ->when(Request()->get('position'), function ($q, $fill) {
                    $q->where('posi.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('dependency'), function ($q, $fill) {
                    $q->where('depe.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('group'), function ($q, $fill) {
                    $q->where('gr.id', 'like', '%' . $fill . '%');
                })
                ->join('companies as co', function ($join) {
                    $join->on('co.id', '=', 'w.company_id');
                })
                ->when(Request()->get('company'), function ($q, $fill) {
                    $q->where('co.id', 'like', '%' . $fill . '%');
                })
                ->orderBy('p.first_name')
                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }


    public function contractsToExpire()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 2;
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname',
                    'p.image',
                    'w.date_of_admission',
                    'w.date_end'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereNotNull('date_end')->whereBetween('date_end', [Carbon::now(), Carbon::now()->addDays(45)])->orderBy('name', 'Desc');
                })
                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }

    public function getPreliquidated()
    {
        $people = DB::table('people as p')
            ->select(
                'p.id',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'p.image',
                'posi.name',
                'p.updated_at',
                'posi.name as position'
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id');
            })
            ->join('positions as posi', function ($join) {
                $join->on('posi.id', '=', 'w.position_id');
            })
            ->where('status', 'PreLiquidado')
            ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1));
        /* for ($i = 0; $i < count($people); $i++) {
                $fecha = $people[$i]->updated_at;
                // dd($fecha);
                // $fechas = Carbon::parse()->locale('es')->diffForHumans();
            } */
        return $this->success($people);
    }

    public function getLiquidated($id)
    {
        return $this->success(
            Person::with('work_contract')->where('id', '=', $id)->first()
        );
    }

    public function getTrialPeriod()
    {
        $contratoIndeDefinido = DB::table('people as p')
            ->select(
                'p.image',
                'w.id',
                'w.date_of_admission',
                DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
                DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 60 DAY),"%m-%d") as dates'),
                DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days'),
            )
            ->where('wt.conclude', '=', 0)
            ->where('p.status', '=', 'Activo')
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('work_contract_types as wt', function ($join) {
                $join->on('wt.id', '=', 'w.work_contract_type_id');
            })
            ->havingBetween('worked_days', [40, 60]);

        $contratoFijo = DB::table('people as p')
            ->select(
                'p.image',
                'w.id',
                'w.date_of_admission',
                DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
                DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 36 DAY),"%m-%d") as dates'),
                DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days')
            )
            ->where('wt.conclude', '=', 1)
            ->where('p.status', '=', 'Activo')
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('work_contract_types as wt', function ($join) {
                $join->on('wt.id', '=', 'w.work_contract_type_id');
            })
            ->whereRaw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) <=TRUNCATE( (TIMESTAMPDIFF (DAY, w.date_of_admission, w.date_end) / 5),0)')
            ->union($contratoIndeDefinido)
            ->get();
        return $this->success(
            $contratoFijo
        );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    use ApiResponser;

    public function show($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id as person_id',
                    'posi.name as position_name',
                    'd.name as dependency_name',
                    'gr.name as group_name',
                    'd.group_id',
                    'w.rotating_turn_id',
                    'posi.dependency_id',
                    'f.name as fixed_turn_name',
                    'c.name as company_name',
                    'w.turn_type',
                    'w.position_id',
                    'w.company_id',
                    'w.fixed_turn_id',
                    'w.id'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as d', function ($join) {
                    $join->on('d.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'd.group_id');
                })
                ->leftJoin('fixed_turns as f', function ($join) {
                    $join->on('f.id', '=', 'w.fixed_turn_id');
                })
                ->join('companies as c', function ($join) {
                    $join->on('c.id', '=', 'w.company_id');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function updateEnterpriseData(Request $request)
    {
        try {
            $work_contract = WorkContract::find($request->get('id'));
            $work_contract->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function pdf($id)
    {
        /* $data = WorkContract::show($id); */
        $company = Company::where('id', 1)->first();
        $funcionario =
            DB::table('people')
            ->where('id', '=', $id)
            ->first();

        $contract =
            DB::table('work_contracts')
            ->where('person_id', '=', $id)
            ->first();

        $position =
            DB::table('positions')
            ->where('id', '=', $contract->position_id)
            ->first();
        $numberFormat = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $enLetras = $numberFormat->format($contract->salary);
        $fechaInicio = Carbon::parse($contract->date_of_admission)->locale('es')->translatedFormat('j F Y');
        $fechaFin = Carbon::parse($contract->date_end)->locale('es')->translatedFormat('j F Y');
        $fechaI = new DateTime($contract->date_of_admission);
        $fechain = date($contract->date_of_admission);
        $fechaF = new DateTime($contract->date_end);
        $fechaCreado = Carbon::parse($contract->created_at)->locale('es')->translatedFormat('j F Y');
        $diferencia = $fechaI->diff($fechaF);
        $prueba = $diferencia->format("%m") * 30 / 5;
        if ($prueba > 60) {
          $prueba = 60;
        }
        $fechaFinPrueba = Carbon::parse(date("j F Y",strtotime($fechain." + $prueba days")))->locale('es')->translatedFormat('j F Y'); 
        echo $fechaFinPrueba;
        $created = new Carbon($contract->created_at);
        $dia = $created->format('d');
        $mes = Carbon::parse($created)->locale('es')->translatedFormat('F');
        $year = Carbon::parse($created)->locale('es')->translatedFormat('Y');
        $diaenletras = $numberFormat->format($dia);
        $diferenciaLetras = $numberFormat->format($diferencia->format("%m"));
        $contenifoFijo = '<!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <style>
              table.table tbody tr td {
                border: 1px solid black;
              }
            </style>
          </head>
          <body style="width: 700px">
            <div style="text-align: center">
              <strong>
                CONTRATO INDIVIDUAL DE TRABAJO A TÉRMINO FIJO INFERIOR A UN AÑO
              </strong>
            </div>
            <table class="table" style="margin-top: 30px; margin-bottom: 30px; text-transform: uppercase;">
              <tbody>
                <tr>
                  <td colspan="2" style="text-align: center">
                    <strong><em>EL EMPLEADOR</em></strong>
                  </td>
                </tr>
                <tr>
                  <td style="width: 350px">El Empleador:</td>
                  <td>' . $company->social_reason .'</td>
                </tr>
                <tr>
                  <td>NIT:</td>
                  <td>' . $company->document_number . '-' . $company->verification_digit . '</td>
                </tr>
                <tr>
                  <td>Dirección Principal:</td>
                  <td>CRA 16 # 60-14 La Esmeralda</td>
                </tr>
                <tr>
                  <td>Ciudad:</td>
                  <td>Girón (Santander)</td>
                </tr>
                <tr>
                  <td>Representante Legal:</td>
                  <td>ALBERTO BALCARCEL ZAMBRANO</td>
                </tr>
                <tr>
                  <td>Cédula:</td>
                  <td>13.835.833</td>
                </tr>
                <tr>
                  <td style="text-align: center" colspan="2">
                    <strong><em>EL TRABAJADOR</em></strong>
                  </td>
                </tr>
                <tr>
                  <td>El Trabajador:</td>
                  <td>' . $funcionario->first_name . ' ' . $funcionario->second_name . ' ' . $funcionario->first_surname . ' ' . $funcionario->second_surname . '</td>
                </tr>
                <tr>
                  <td>Cédula:</td>
                  <td>' . number_format($funcionario->identifier, 0, "", ".") . '</td>
                </tr>
                <tr>
                  <td>Dirección:</td>
                  <td>' . $funcionario->direction . '</td>
                </tr>
                <tr>
                  <td>Teléfono:</td>
                  <td>'. $funcionario->cell_phone.'</td>
                </tr>
                <tr>
                  <td>Correo electrónico:</td>
                  <td>
                  '. $funcionario->email.'
                  </td>
                </tr>
                <tr>
                  <td colspan="2" valign="bottom" style="text-align: center">
                    <strong><em>DEL CONTRATO</em></strong>
                  </td>
                </tr>
                <tr>
                  <td>Cargo contratado:</td>
                  <td valign="bottom">'. $position->name . '</td>
                </tr>
                <tr>
                  <td>Salario mensual:</td>
                  <td valign="bottom">
                  $' . number_format($contract->salary, 0, "", ".")  
                  . ' (' . $enLetras . ' MCTE)
                  </td>
                </tr>
                <tr>
                  <td>Forma de pago:</td>
                  <td valign="bottom">QUINCENAL</td>
                </tr>
                <tr>
                  <td>Término del contrato:</td>
                  <td valign="bottom">'.$diferencia->format("%m").' meses</td>
                </tr>
                <tr>
                  <td>Fecha de Inicio:</td>
                  <td valign="bottom">' . $fechaInicio. '</td>
                </tr>
                <tr>
                  <td>Fecha de Terminación:</td>
                  <td valign="bottom">' . $fechaFin. '</td>
                </tr>
                <tr>
                  <td>PERIODO DE PRUEBA:</td>
                  <td valign="bottom">'.$prueba.' DIAS</td>
                </tr>
                <tr>
                  <td>FECHA DE PERIODO DE PRUEBA:</td>
                  <td valign="bottom">
                    '.$prueba.' DIAS (' . $fechaInicio. ' AL '.$fechaFinPrueba.')
                  </td>
                </tr>
                <tr>
                  <td>Lugar de la prestación del servicio:</td>
                  <td valign="bottom">GIRÓN (SANTANDER)</td>
                </tr>
              </tbody>
            </table>
            <p>
              Entre el empleador y el trabajador, identificados en el anterior recuadro,
              convienen libre y espontáneamente celebrar por medio del presente
              documento
              <strong>
                CONTRATO INDIVIDUAL DE TRABAJO A TERMINO FIJO INFERIOR A UN AÑO
              </strong>
              , el cual se regirá por las siguientes:
            </p>
            <p align="center">
              <strong>CLÁUSULAS</strong>
            </p>
            <p>
              Entre el empleador y el trabajador, de las condiciones ya dichas
              identificados como aparece al pie de sus correspondientes firmas se ha
              celebrado el presente contrato individual de trabajo, regido además por
              las siguientes cláusulas: <strong><u>PRIMERA</u>.</strong>
              <strong>LABOR CONTRATADA</strong>: El empleador contrata al trabajador
              para desempeñarse como <strong>'. $position->name . '</strong> acorde
              con el manual de funciones anexo y parte integral del presente contrato,
              donde deberá ejecutar las labores y actividades asignadas por el EMPLEADOR
              o su representante, las cuales debe realizar con calidad y cumplimiento y
              el TRABAJADOR se obliga a: a) A poner al servicio del EMPLEADOR toda su
              capacidad normal del trabajo, en forma exclusiva en el desempeño de las
              funciones propias del oficio mencionado y en las labores anexas y
              complementarias del mismo, de conformidad con las órdenes e instrucciones
              que le imparta EL EMPLEADOR o sus representantes, y b) A no prestar
              directa ni indirectamente servicios laborales a otros EMPLEADORES, ni a
              trabajar por cuenta propia en el mismo oficio, durante la vigencia de este
              contrato. <strong><u>SEGUNDA.</u></strong>
              <strong>ELEMENTOS DE TRABAJO</strong>: Corresponde al empleador
              suministrar los elementos necesarios para el normal desempeño de las
              funciones del cargo contratado, donde el trabajador se compromete a
              utilizar todos los elementos suministrados por el empleador solo para uso
              estrictamente laboral.
              <strong> <u>TERCERA. </u>OBLIGACIONES DEL TRABAJADOR<u>:</u> </strong>El
              trabajador se obliga: a) A poner al servicio del empleador toda su
              capacidad normal de trabajo, en forma exclusiva en el desempeño de las
              funciones propias del oficio mencionado y las labores anexas y
              complementarias del mismo, de conformidad con las órdenes e instrucciones
              que le imparta el empleador o sus representantes, y b) A no prestar
              directa ni indirectamente servicios laborales a otros empleadores, ni a
              trabajar por cuenta propia en el mismo oficio, durante la vigencia de este
              contrato. c) Aceptar la reubicación que le ordene la empresa, en razón a
              que por la modalidad productiva todo trabajador debe estar capacitado para
              desempeñar todas las labores propias de la línea de producción o de la
              actividad empresarial. d) A no comunicar con terceros, salvo autorización
              expresa, las informaciones que tengan sobre su trabajo, especialmente
              sobre las cosas que sean de naturaleza reservada o cuya divulgación pueda
              ocasionar perjuicios al empleador, lo que no obsta para denunciar delitos
              comunes o violaciones del contrato o de las normas legales del trabajo
              ante las autoridades competentes. e) Conservar y restituir en buen estado,
              salvo el deterioro natural, los instrumentos y útiles que le hayan sido
              facilitados y las materias primas sobrantes. f) Guardar rigurosamente la
              moral en las relaciones con sus superiores y compañeros. g) Comunicar
              oportunamente al empleador las observaciones que estime conducentes a
              evitarle daños y perjuicios. h) Prestar la colaboración posible en casos
              de siniestro o de riesgo inminente que afecten o amenacen las personas o
              las cosas de la empresa o establecimiento. i) Observar las medidas
              preventivas higiénicas, prescritas por el médico del empleador o por las
              autoridades del ramo. j) Observar con suma diligencia y cuidado las
              instrucciones y órdenes preventivas de accidentes o de enfermedades
              profesionales. k) Comunicar a su jefe inmediato cualquier falla humana,
              física o mecánica, que se presente en la realización del trabajo, para
              tomar medidas correctivas. l) Usar siempre implementos de seguridad que la
              empresa les proporciona. m) Buscar y recibir instrucciones sobre la manera
              correcta como debe ser manejados los instrumentos dados para su uso, a fin
              de evitar y prevenir todo riesgo o error. n) Desarrollar las gestiones que
              se le encomienden con rapidez y eficiencia. o) Racionalizar el tiempo que
              dedica para realizar cada una de las actividades encomendadas. p) Cumplir
              a cabalidad con las funciones descritas en el manual de funciones, así
              como con los indicadores de gestión. Tal documento denominado “manual de
              funciones” constituye un anexo al presente contrato y hace parte integral
              del mismo luego de suscrito por el trabajador en señal de aceptación.
              <strong>Parágrafo Primero: </strong>Además de las prohibiciones de orden
              legal y reglamentario, las partes estipulan las siguientes prohibiciones
              especiales al trabajador(a): a) Solicitar préstamos especiales o ayuda
              económica a los clientes del empleador aprovechándose de su cargo u oficio
              o aceptarles donaciones de cualquier clase sin la previa autorización
              escrita del empleador; b) Autorizar o ejecutar sin ser de su competencia,
              operaciones que afecten los intereses del empleador o negociar bienes y/o
              mercancías del empleador en provecho propio; c) Retener dinero o hacer
              efectivos cheques recibidos para el empleador; d) Presentar cuentas de
              gastos ficticias o reportar como cumplidas tareas no efectuadas; e)
              Cualquier actitud en los compromisos comerciales, personales en las
              relaciones sociales, que pueda afectar en forma nociva la reputación del
              empleador, y f) Retirar de las instalaciones donde funcione la empresa
              elementos, máquinas y útiles de propiedad del empleador sin su
              autorización escrita. <strong>Parágrafo Segundo: </strong>Las partes
              declaran que en el presente contrato se entienden incorporadas, en lo
              pertinente, las disposiciones legales que regulan las relaciones entre la
              empresa y sus trabajadores, en especial las del contrato de trabajo para
              el oficio que se suscribe, fuera de las obligaciones consignadas en los
              reglamentos de trabajo o de higiene y de seguridad industrial de la
              empresa. <strong><u>CUARTA</u>.</strong> <strong>REMUNERACIÓN: </strong>El
              empleador pagará al trabajador por la prestación de sus servicios el
              salario mensual<strong
                > $' . number_format($contract->salary, 0, "", ".")  
                . ' (' . $enLetras . ' MCTE)</strong
              >
              <strong> </strong>pagadero en las oportunidades ya señaladas. Dentro de
              este pago se encuentra incluida la remuneración de los descansos
              dominicales y festivos de que tratan los capítulos I y II del título VII
              del Código Sustantivo del Trabajo. <strong>Parágrafo Primero.</strong> Si
              por cualquier circunstancia el trabajador prestare su servicio en día
              dominical o festivo, no tendrá derecho a sobre remuneración alguna, si tal
              trabajo no hubiere sido autorizado por el empleador, previamente y por
              escrito. Cuando la necesidad de este trabajo se presente de manera
              imprevista o inaplazable, deberá ejecutarse y darse cuenta de él por
              escrito, a la mayor brevedad, al empleador o sus representantes. El
              empleador, en consecuencia, no reconocerá ningún trabajo suplementario o
              en días de descanso legalmente obligatorio que no haya sido autorizado
              previamente o avisado inmediatamente, como queda dicho.
              <strong>Parágrafo segundo.</strong> El empleador no suministra ninguna
              clase de salario en especie. <strong>Parágrafo tercero.</strong> Cuando
              por causa emanada directa o indirectamente de la relación contractual
              existan obligaciones de tipo económico a cargo del trabajador y a favor
              del empleador, éste procederá a efectuar las deducciones a que hubiere
              lugar de las sumas a cancelar por concepto de liquidación de salarios, en
              cualquier tiempo y, más concretamente, a la terminación del presente
              contrato, ya que así lo autoriza desde ahora y para toda la relación
              laboral el trabajador, entendiendo expresamente las partes que la presente
              autorización cumple las condiciones de orden escrita previa, de acuerdo a
              lo ordenado legalmente y aplicable para cada caso.
              <strong><u>QUINTA</u></strong
              >. <strong>ELEMENTOS NO CONSTITUTIVOS DE SALARIO: </strong>Las partes
              expresamente acuerdan que lo que reciba el trabajador o llegue a recibir
              en el futuro, adicional a su salario ordinario, ya sean beneficios o
              auxilios habituales u ocasionales, tales como alimentación, habitación o
              vestuario, bonificaciones ocasionales o cualquier otra que reciba, durante
              la vigencia del contrato de trabajo, en dinero o en especie, no
              constituyen salario y se tendrá como mera liberalidad y por este motivo no
              se tendrá como factor salarial para liquidación de prestaciones, aportes a
              seguridad social. En concordancia con el artículo 128 del CST y el
              artículo 30 de la ley 1393 del 2010. <strong><u>SEXTA</u></strong
              >. <strong>HORARIO DE TRABAJO: </strong>El trabajador se obliga a laborar
              la jornada ordinaria en los turnos y dentro de las horas señaladas por el
              empleador, pudiendo hacer éste ajustes o cambios de horario cuando lo
              estime conveniente, previa autorización del Empleador. Por el acuerdo
              expreso o tácito de las partes, podrán repartirse las horas jornada
              ordinaria de la forma prevista en el artículo 164 del Código Sustantivo
              del Trabajo, modificado por el artículo 23 de la Ley 50 de 1990, teniendo
              en cuenta que los tiempos de descanso entre las secciones de la jornada no
              se computan dentro de la misma, según el artículo 167 ibídem.<strong
                ><u>SEPTIMA</u>.
              </strong>
              <strong>DURACIÓN DEL CONTRATO<u>:</u></strong> El presente contrato se
              celebra por el término de <strong>'.$diferenciaLetras.' ('.$diferencia->format("%m").') MESES</strong
              ><strong>.</strong> <strong> </strong>Si antes de la fecha de vencimiento
              de este término, ninguna de las partes avisare por escrito a la otra de su
              determinación de no prorrogar el contrato, con antelación no inferior a 30
              días, este se entenderá prorrogado por un periodo igual al inicialmente
              pactado. Tratándose de un contrato a término fijo inferior a un año,
              únicamente podrá prorrogarse sucesivamente el contrato hasta por tres (3)
              periodos iguales o inferiores, al de los cuales los términos de renovación
              no podrán ser inferior a un año y así sucesivamente.
              <strong>Parágrafo Primero: PERIODO DE PRUEBA</strong>: El periodo de
              prueba se estipula por escrito, por lo que, en los contratos de trabajo a
              término fijo cuya duración sea inferior a un año, el periodo de prueba no
              podrá ser superior a la quinta parte del término inicialmente pactado para
              el respectivo contrato. <strong><u>OCTAVA</u></strong
              ><strong>: </strong>
              <strong>
                JUSTAS CAUSAS PARA DAR POR TERMINADO LA RELACION CONTRACTUAL
              </strong>
              Son justas causas para dar por terminado unilateralmente este contrato por
              cualquiera de las partes, las enumeradas en los artículos 62 y 63 del
              Código Sustantivo del Trabajo, el incumplimiento de las obligaciones y
              prohibiciones que se expresan en los artículos 57 y siguientes del código
              sustantivo del trabajo, el incumplimiento del reglamento interno y el de
              seguridad industrial; y, además, por parte del empleado, las faltas que
              para el efecto se califiquen como graves en el espacio reservado para las
              cláusulas adicionales en el presente contrato.
              <strong>Parágrafo Primero:</strong> LIQUIDACIÓN Y ENTREGA DE ACREENCIAS
              LABORALES: En consideración a que toda liquidación y pago de salarios y
              prestaciones sociales exige varios días para obtener los datos e informes
              necesarios y así mismo, implica su revisión, realizar, aprobar la
              liquidación definitiva girar los cheques correspondientes, etc. Las partes
              de común acuerdo aclaran que existe un tiempo prudencial y razonable para
              estos efectos, tal como en su oportunidad lo manifiesto la Corte Suprema
              de Justicia. Dentro de este plazo prudencial y de buena fe EL EMPLEADOR
              podrá liquidar y pagar los salarios y/o prestaciones sociales sin que por
              ello incurra en mora de ninguna naturaleza ni quede obligado a pagar la
              indemnización moratoria contemplada de la ley 789 de 2002.
            </p>
            <p>
              De igual manera se deja expresa constancia de la especial obligación del
              TRABAJADOR de tramitar directamente su paz y salvo empresarial, documento
              sin el cual no se pueden tramitar las acreencias laborales que le
              correspondan y después del tiempo prudencial del caso, se procederá a la
              consignación de depósito judicial en el banco Agrario de Colombia.
              <strong>Parágrafo Segundo: </strong>Cuando el contrato de trabajo termine
              por cualquier motivo, es responsabilidad especial del TRABAJADOR hacer
              entrega de su cargo a través de un ACTA DE FINALIZACIÓN, en la cual se
              detallen los asuntos pendientes y se sugieran las acciones que se deban
              ejecutar para el normal desarrollo de la responsabilidad del cargo. Si lo
              anterior no se presenta, la organización iniciará las acciones legales
              pertinentes por los perjuicios causados <strong>Tercero</strong>: El
              empleador y el trabajador acuerdan que el salario base de liquidación para
              el pago de la seguridad social integral y de las prestaciones sociales,
              será el 100% del total de la remuneración mensual recibida por el
              trabajador, en concordancia con el artículo 128 del CST y el artículo 30
              de la ley 1393 del 2010.
              <strong><u>NOVENA: </u>INVENCIONES O DESCUBRIMIENTOS: </strong>Las
              invenciones o descubrimientos realizados por el trabajador contratado para
              investigar pertenecen al empleador, de conformidad con el artículo 539 del
              Código de Comercio, así como el artículo 20 y concordantes de la ley 23 de
              1982 sobre derechos de autor. En cualquier otro caso el invento pertenece
              al trabajador, salvo cuando éste no haya sido contratado para investigar y
              realice la invención mediante datos o medios conocidos o utilizados en
              razón de la labor desempeñada, evento en el cual el trabajador, tendrá
              derecho a una compensación que se fijará dé acuerdo con el monto del
              salario, la importancia del invento o descubrimiento, el beneficio que
              reporte al empleador u otros factores similares.
              <strong><u>DÉCIMA: </u>LUGAR DE PRESTACIÓN DEL SERVICIO:</strong> Las
              partes podrán convenir que el trabajo se preste en lugar distinto al
              inicialmente contratado, siempre que tales traslados no desmejoren las
              condiciones laborales o de remuneración del trabajador, o impliquen
              perjuicios para él. Los gastos que se originen con el traslado serán
              cubiertos por el empleador de conformidad con el numeral 8º del artículo
              57 del Código Sustantivo del Trabajo. El trabajador se obliga a aceptar
              los cambios de oficio que decida el empleador dentro de su poder
              subordinante, siempre que se respeten las condiciones laborales del
              trabajador y no se le causen perjuicios. Todo ello sin que se afecte el
              honor, la dignidad y los derechos mínimos del trabajador, de conformidad
              con el artículo 23 del Código Sustantivo del Trabajo, modificado por el
              artículo 1º de la Ley 50 de 1990” <strong>Parágrafo Primero</strong>:
              TRATAMIENTO DE VIATICOS TRASLADO. Las partes podrán convenir que el
              trabajo se preste en lugar distinto al inicialmente contratado, siempre
              que tales traslados no se desmejoren las condiciones laborales o de
              remuneración del trabajador, o impliquen perjuicios para en él. Los gastos
              que se originen con el traslado serán cubiertos por el empleador de
              conformidad con el numeral 8º del artículo 57 del Código Sustantivo del
              Trabajo. El trabajador se obliga a aceptar los cambios de lugar y de
              oficio que decida el empleador dentro de su poder subordinante, siempre
              que se respeten las condiciones laborales del trabajador y no se le causen
              perjuicios. Todo ello sin que se afecte el honor, la dignidad y los
              derechos mínimos del trabajador, de conformidad con el artículo 23 del
              Código Sustantivo del trabajo, modificado por el artículo 1º de la Ley 50
              de 1990. <strong><u>DÉCIMA PRIMERA: </u>ELEMENTOS DE TRABAJO: </strong>Los
              instrumentos especiales de trabajo que se suministran al trabajador, tales
              como uniformes, teléfonos, papelería, etc., únicamente pueden ser
              utilizados por el trabajador para el desempeño de su función durante sus
              horas de trabajo, quedando expresamente prohibidos trasladarlos del sitio
              de trabajo o prestarlos. Si el trabajador contrariare lo aquí dispuesto
              este será el único responsable civil, policiva y penalmente, por las
              acciones que se llegaren a cometer. Queda obligado el trabajador a
              devolver dichos instrumentos de trabajo al empleador en el mismo estado en
              que le fueron entregados a la terminación del contrato de trabajo, salvo
              deterioro natural de los instrumentos de trabajo.
              <strong>Parágrafo segundo: </strong>Dentro de las obligaciones
              contractuales del trabajador, está la custodia de los bienes entregados
              por el EMPLEADOR, para el desarrollo de su actividad laboral y de aquellos
              que sean parte del giro ordinario de la actividad comercial del empleador.
            </p>
            <p>
              <strong>Parágrafo tercero: </strong>
              Es facultad del empleador, realizar inventarios de los bienes entregados
              en custodia y en caso de pérdida de los mismos el empleador daría un valor
              equivalente en dinero de acuerdo a la característica de los mismos y los
              precios del mercado o de los proveedores de estos.
              <strong><u>DÉCIMA SEGUNDA: </u>PROTOCOLO DE SERVICIO:</strong> El empleado
              se obliga para con el patrono a seguir al pie de la letra el protocolo de
              servicio que ha sido entregado por la empresa, así como también observará
              y cumplirá con las instrucciones que se dan de acuerdo a la naturaleza de
              las funciones a realizar.
              <strong><u>DÉCIMA TERCERA.</u> CONFIDENCIALIDAD:</strong> El trabajador
              reconoce que, en virtud de su relación contractual, podrá tener acceso a
              información y materiales relacionados con los planes de negocio,
              tecnologías o estrategias de marketing de gran valor del empleador, razón
              por la cual se compromete a no utilizar esta información en su propio
              provecho ni en el de terceros.<strong> </strong>En concordancia con lo
              establecido en el numeral 2 del artículo 58, del Código Sustantivo del
              Trabajo, durante la vigencia del presente contrato y de manera indefinida,
              el empleado se obliga y se compromete a no comunicar a terceros, ni usar
              en su propio provecho, las informaciones de naturaleza reservada que tenga
              o llegue a conocer en razón de su vinculación laboral y cuya divulgación
              pueda ocasionar perjuicios a la empresa. Como consecuencia de lo señalado
              en la presente cláusula se considera FALTA GRAVE por parte del EMPLEADO y
              se configura justa causa para terminar el contrato de trabajo, sin
              perjuicio del cobro económico por los perjuicios ocasionados a los que
              haya lugar. <strong><u>DÉCIMA CUARTA. </u></strong>
              <strong>SITUACIONES DE EMBRIAGUEZ Y SITUACIONES ANÁLOGAS:</strong> Por la
              alta responsabilidad asumida en el manejo de las funciones propias del
              cargo, está absolutamente prohibido y se considera falta grave, el
              consumir bebida alcohólica o estar bajo el efecto de drogas alucinógenas,
              en cualquier momento en que el TRABAJADOR esté bajo la responsabilidad del
              cargo, al igual que presentarse al trabajo en las condiciones inadecuadas
              aún por la primera vez, también es prohibido fumar en sitios cerrados o
              prohibidos por la empresa. <strong><u>DECIMA </u></strong
              ><strong><u>QUINTA. </u></strong>
              <strong>AFILIACION Y PAGO A SEGURIDAD SOCIAL: </strong>Es obligación del
              empleador afiliar al trabajador a la seguridad social como es salud,
              pensión y riesgos profesionales, autorizando el trabajador el descuento de
              su salario, los valores que le correspondan aportar, en la proporción
              establecida por la ley. <strong><u>DECIMA SEXTA.</u></strong
              ><u> </u> <strong>INTERPRETACIÓN DEL CONTRATO:</strong> Este contrato ha
              sido redactado estrictamente de acuerdo con la ley y la jurisprudencia y
              será interpretado de buena fe y en consonancia con el Código Sustantivo
              del Trabajo cuyo objeto, definido en su artículo 1°, es lograr la justicia
              en las relaciones entre empleadores y trabajadores dentro de un espíritu
              de coordinación económica y equilibrio social.
              <strong> <u>DECIMA SEPTIMA.</u> MODIFICACIONES:</strong> El presente
              contrato reemplaza en su integridad y deja sin efecto alguno cualquiera
              otro contrato verbal celebrado entre las partes con anterioridad.
              <strong><u>DECIMA OCTAVA.</u> </strong>Las partes con plena y total
              autonomía y en forma libre y sin presión de ninguna naturaleza convienen
              en definir que el trabajador deberá ser polivalente y multifuncional, lo
              cual significa que dentro de sus labores podrá desempeñar, cuando así lo
              disponga la empresa cualquier otra función que tenga relación con las
              tareas que le han sido asignadas. <strong><u>DECIMA NOVENA</u></strong>
              <strong><u> </u></strong>Este contrato ha sido redactado estrictamente de
              acuerdo con la ley y la jurisprudencia y será interpretado de buena fe y
              en concordancia con el Código Sustantivo del Trabajo cuyo objeto, definido
              en su artículo 1º, es lograr la justicia en las relaciones entre
              empleadores y trabajadores dentro de un espíritu de coordinación económica
              y equilibrio social. <strong><u>VIGÉSIMA </u></strong>Las partes declaran
              que en el presente contrato se entienden incorporadas, en lo pertinente,
              las disposiciones legales que regulan las relaciones entre la empresa y
              sus trabajadores, en especial, las del contrato de trabajo para el oficio
              que se suscribe, fuera de las obligaciones consignadas en los reglamentos
              de trabajo y demás reglamentos de la empresa.
            </p>
            <p>
              Para constancia se firma en dos o más ejemplares del mismo tenor y valor.
            </p>
            <p>
              En constancia de lo anterior se firma en dos o más ejemplares del mismo
              tenor y valor, en la ciudad de Girón (Santander), el ' . $diaenletras 
              . ' ('. $dia . ') de '. $mes .' de '.$year.'<u></u>
            </p>
            <table style="width: 100%; margin-top: 30px">
              <thead style="text-align: left">
                <tr style="text-align: left">
                  <th style="text-align: left"><strong>EL EMPLEADOR</strong></th>
                  <th style="text-align: left"><strong>EL EMPLEADO</strong></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="padding-top: 40px">
                    ____________________________________
                  </td>
                  <td style="padding-top: 40px">
                    ____________________________________
                  </td>
                </tr>
                <tr>
                  <td>ALBERTO LUIS BALCARCEL</td>
                  <td>' . $funcionario->first_name . ' ' . $funcionario->second_name . ' ' . $funcionario->first_surname . ' ' . $funcionario->second_surname . '</td>
                </tr>
                <tr>
                  <td>C.C. Nº 13.835.833</td>
                  <td>C.C. '. $funcionario->identifier .'</td>
                </tr>
                <tr>
                  <td>Representante Legal</td>
                  <td></td>
                </tr>
                <tr>
                  <td>MAQUINADOS &amp; MONTAJES S.A.S.</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </body>
        </html>
        
        ';
        $contenidoObraLaboral = '
    
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <style>
            table.table tbody tr td {
                border: 1px solid black;
            }
            </style>
        </head>
        <body style="width: 700px;"">
            <div style="text-align: center">
                <strong 
                >CONTRATO LABORAL POR EL TÉRMINO DE LA OBRA O LABOR CONTRATADA
                </strong>
            </div>
            <table class="table" style="margin-top: 30px; margin-bottom: 30px; text-transform: uppercase;">
            <tbody>
                <tr>
                <td colspan="2" style="text-align: center">
                    <strong><em>EL EMPLEADOR</em></strong>
                </td>
                </tr>
                <tr>
                <td style="width: 50%;">El Empleador:</td>
                <td>' . $company->social_reason .'</td>
                </tr>
                <tr>
                <td>NIT:</td>
                <td>' . $company->document_number . '-' . $company->verification_digit . '</td>
                </tr>
                <tr>
                <td>Direcci&oacute;n:</td>
                <td>Cra 16 # 60-14 La Esmeralda</td>
                </tr>
                <tr>
                <td>Ciudad:</td>
                <td>GIR&Oacute;N, SANTANDER</td>
                </tr>
                <tr>
                <td>Representante Legal:</td>
                <td>ALBERTO LUIS BALCARCEL ZAMBRANO</td>
                </tr>
                <tr>
                <td>C&eacute;dula:</td>
                <td>13.835.833 DE BUCARAMANGA</td>
                </tr>
                <tr>
                <td colspan="2" style="text-align: center">
                    <strong><em>EL TRABAJADOR</em></strong>
                </td>
                </tr>
                <tr>
                <td>El Trabajador:</td>
                <td>' . $funcionario->first_name . ' ' . $funcionario->second_name . ' ' . $funcionario->first_surname . ' ' . $funcionario->second_surname . '</td>
                </tr>
                <tr>
                <td>C&eacute;dula:</td>
                <td>' . number_format($funcionario->identifier, 0, "", ".") . '</td>
                </tr>
                <tr>
                <td>Direcci&oacute;n:</td>
                <td>' . $funcionario->direction . '</td>
                </tr>
                <tr>
                <td>Ciudad:</td>
                <td>'. $funcionario->place_of_birth.'</td>
                </tr>
                <tr>
                <td>Tel&eacute;fono:</td>
                <td>'. $funcionario->cell_phone.'</td>
                </tr>
                <tr>
                <td colspan="2" style="text-align: center">
                    <strong><em>DEL CONTRATO</em></strong>
                </td>
                </tr>
                <tr>
                <td>Salario mensual:</td>
                <td>$' . number_format($contract->salary, 0, "", ".")  
                . ' (' . $enLetras . ' MCTE)</td>
                </tr>
                <tr>
                <td>Auxilio de transporte:</td>
                <td>
                    117.172 (CIENTO DIECISIETE MIL CIENTO SETENTA Y DOS PESOS MCTE)
                </td>
                </tr>
                <tr>
                <td>Forma de pago:</td>
                <td>QUINCENAL</td>
                </tr>
                <tr>
                <td>Cargo y labor contratada:</td>
                <td>
                    EL TRABAJADOR DESEMPE&Ntilde;AR&Aacute; LA LABOR DE '. $position->name . '
                </td>
                </tr>
                <tr>
                <td>Fecha de Inicio:</td>
                <td>' . $fechaInicio. '</td>
                </tr>
                <tr>
                <td>Fecha de Terminaci&oacute;n:</td>
                <td>
                    A LA TERMINACI&Oacute;N TOTAL O PARCIAL DE LA OBRA O LABOR
                    CONTRATADA
                </td>
                </tr>
                <tr>
                <td>Fecha de Suscripci&oacute;n:</td>
                <td>' . $fechaCreado . '</td>
                </tr>
            </tbody>
            </table>

            Entre <strong>EL EMPLEADOR</strong> y <strong>EL TRABAJADOR</strong>, de las
            condiciones ya dichas, identificados como aparece al pie de sus firmas, se
            ha celebrado el presente
            <strong
            >CONTRATO DE TRABAJO POR EL T&Eacute;RMINO DE LA OBRA O LABOR
            CONTRATADA</strong
            >, regido adem&aacute;s por las siguientes
            <strong>CL&Aacute;USULAS: </strong>

            <strong><u>PRIMERA. LABOR CONTRATADA:</u></strong> EL EMPLEADOR contrata al
            TRABAJADOR para desempe&ntilde;arse como se indic&oacute; en el recuadro
            inicial, donde deber&aacute; ejecutar las labores y actividades asignadas
            por el EMPLEADOR o su representante, las cuales se encuentran estipuladas en
            el recuadro inicial y que tambi&eacute;n ser&aacute;n puestas de presente al
            TRABAJADOR en desarrollo del presente contrato, donde claramente las
            indicaciones recibidas tendr&aacute;n relaci&oacute;n directa con el cargo
            contratado conforme al MANUAL DE FUNCIONES Anexo N&ordm; 1 y parte integral
            del presente documento. <strong><u>SEGUNDA. </u></strong
            ><strong><u>OBLIGACIONES DEL TRABAJADOR:</u></strong> El empleador contrata
            los servicios personales del trabajador y &eacute;ste se obliga:
            <strong>1. </strong>Aceptar y efectuar las labores encomendadas por EL
            EMPLEADOR, que deber&aacute;n estar a su vez, vinculadas con el cargo o
            labor contratada. <strong>2. </strong>A no comunicar con terceros, salvo
            autorizaci&oacute;n expresa, las informaciones que tengan sobre su trabajo,
            especialmente sobre los conceptos que sean de naturaleza reservada o cuya
            divulgaci&oacute;n pueda ocasionar perjuicios al empleador, lo que no obsta
            para denunciar delitos comunes o violaciones del contrato o de las normas
            legales del trabajo ante las autoridades competentes.
            <strong>3. </strong>Conservar y restituir en buen estado, salvo el deterioro
            natural, los instrumentos y &uacute;tiles que le hayan sido facilitados y
            las materias primas sobrantes. <strong>4. </strong>Guardar rigurosamente la
            moral en las relaciones con sus superiores y compa&ntilde;eros.
            <strong>5. </strong>Comunicar oportunamente al empleador las observaciones
            que estime conducentes a evitarle da&ntilde;os y perjuicios.
            <strong>6. </strong>Observar las medidas preventivas higi&eacute;nicas,
            prescritas por el m&eacute;dico del empleador o por las autoridades del
            ramo. <strong>7. </strong>Comunicar a su jefe inmediato cualquier falla
            humana, f&iacute;sica o mec&aacute;nica, que se presente en la
            realizaci&oacute;n del trabajo, para tomar medidas correctivas.
            <strong>8. </strong>Usar siempre implementos de seguridad que la empresa les
            proporciona, lo mismo que los overoles y zapatos de labor.
            <strong>9. </strong>Desarrollar las gestiones que se le encomienden con
            rapidez y eficiencia.
            <strong
            ><u>PAR&Aacute;GRAFO PRIMERO. ACUERDO DE CONFIDENCIALIDAD</u>. </strong
            >En concordancia con lo establecido en el numeral 2 del art&iacute;culo 58,
            del C&oacute;digo Sustantivo del Trabajo, durante la vigencia del presente
            contrato y de manera indefinida, el empleado se obliga y se compromete a no
            comunicar a terceros, ni usar en su propio provecho, las informaciones de
            naturaleza reservada que tenga o llegue a conocer en raz&oacute;n de si
            vinculaci&oacute;n laboral y cuya divulgaci&oacute;n pueda ocasionar
            perjuicios a la empresa. Como consecuencia el incumplimiento de lo
            se&ntilde;alado en la presente cl&aacute;usula se considera FALTA GRAVE y
            faculta al EMPLEADOR a dar por terminado el contrato de trabajo bajo justa
            causa. <strong><u>PAR&Aacute;GRAFO SEGUNDO.</u></strong> De acuerdo al
            numeral 2 de la presente cl&aacute;usula, las obligaciones derivadas del
            presente convenio, se considerar&aacute; como una FALTA GRAVE, que
            dar&aacute; lugar a la terminaci&oacute;n del contrato de trabajo o a la
            imposici&oacute;n de las sanciones que libremente determine el empleador, de
            acuerdo con la gravedad de la falta, sin perjuicio del cobro
            econ&oacute;mico por los perjuicios ocasionados a los que haya lugar. El
            TRABAJADOR SE OBLIGA: 1. Restituir al EMPLEADOR todos los elementos de
            trabajo, como: la dotaci&oacute;n, las herramientas, los documentos y todo
            aquello que la compa&ntilde;&iacute;a haya entregado al trabajador para el
            buen desarrollo de la labor contratada; 2. Si se llegare a incumplir alguna
            de las obligaciones con posterioridad a la terminaci&oacute;n del contrato
            de trabajo, EL EMPLEADOR se reserva el derecho de reclamar por la v&iacute;a
            judicial o extrajudicial la indemnizaci&oacute;n de los perjuicios causados,
            para lo cual desde ya se reconoce en el presente contrato; 3. EL TRABAJADOR
            manifiesta que ha sido informado y es consciente que conoce por su trabajo
            informaci&oacute;n confidencial DEL EMPLEADOR, de sus accionistas,
            dem&aacute;s empleados y relacionados, entre otros.
            <strong><u>PAR&Aacute;GRAFO TERCERO.</u></strong
            ><u><strong>REQUERIMIENTO DE AUTORIDADES</strong></u
            >. En caso que alguna autoridad de car&aacute;cter administrativo o judicial
            requiera la presentaci&oacute;n de informaci&oacute;n que pueda
            caracterizarse como de car&aacute;cter confidencial o reservado DEL
            EMPLEADOR, es obligaci&oacute;n del TRABAJADOR dar traslado oportunamente al
            representante legal del EMPLEADOR y/o a la junta directiva del EMPLEADOR
            sobre el requerimiento, antes de divulgar cualquier informaci&oacute;n y
            s&oacute;lo con la autorizaci&oacute;n de &eacute;stos podr&aacute; proceder
            a divulgar dicha informaci&oacute;n.
            <strong><u>TERCERA. PROHIBICIONES DEL TRABAJADOR:</u></strong
            >Se le proh&iacute;be al trabajador lo siguiente:
            <strong>1.</strong> Manejar m&aacute;quinas o veh&iacute;culos distintos de
            aquellas que les han entregado para el desempe&ntilde;o de sus funciones.
            <strong>2.</strong> Levantar o movilizar manualmente cargas que pongan en
            peligro su integridad f&iacute;sica, si &eacute;stas no se ejecutan de
            acuerdo con las instrucciones de seguridad sobre la posici&oacute;n del
            cuerpo de los pies y de los brazos en la manipulaci&oacute;n de cargas.
            <strong>3.</strong> Consumir alimentos o bebidas durante la
            realizaci&oacute;n de la gesti&oacute;n a su cargo.<strong> 4.</strong>
            Consumir y sustraer del &aacute;rea de trabajo o establecimiento, los
            &uacute;tiles de trabajo y las materias primas o productos, sin permiso del
            empleador. <strong>5. </strong>Es absolutamente prohibido y se considera
            falta grave presentarse al trabajo en estado de embriaguez o bajo la
            influencia de narc&oacute;ticos o drogas alucin&oacute;genas.
            <strong>6.</strong> Conservar armas de cualquier clase en el sitio del
            trabajo, a excepci&oacute;n de las que con autorizaci&oacute;n legal puedan
            llevar los celadores<strong>. 7.</strong> Faltar al trabajo sin justa causa
            de impedimento o sin permiso del empleador, excepto en los casos de huelga,
            en los cuales deben abandonar el lugar del trabajo.
            <strong>8.</strong> Hacer colectas, rifas y suscripciones o cualquier clase
            de propaganda en los lugares de trabajo. <strong>9.</strong> Usar los
            &uacute;tiles o herramientas suministradas por el empleador en objetos
            distintos del trabajo contratado<strong>. 10. </strong>Desarrollar
            cuestiones ajenas a las encomendadas.
            <strong><u>PAR&Aacute;GRAFO PRIMERO.</u></strong> Se considera hecho grave
            para EL TRABAJADOR el haber tenido conocimiento o participaci&oacute;n por
            acci&oacute;n, omisi&oacute;n, negligencia o complicidad, en comportamiento
            o hechos delictivos o en contra de la moral, que perjudiquen los intereses
            del empleador o del usuario, sus funcionarios, clientes o allegados, o el
            hecho de tener conocimiento de ellos y no informar oportunamente al
            empleador, sus representantes o al usuario, o dar informaciones falsas o
            extempor&aacute;neas sobre los mismos.
            <strong><u>CUARTA. DURACI&Oacute;N DEL CONTRATO.</u></strong> El presente
            contrato se celebra por el tiempo que dure la realizaci&oacute;n de la obra
            o labor contratada, hasta su terminaci&oacute;n total o parcial.
            <strong><u>QUINTA. PERIODO DE PRUEBA:</u></strong
            >Acuerdan las partes fijar como periodo de prueba el t&eacute;rmino de
            DIECIOCHO (18) d&iacute;as, de conformidad con lo que establece la ley para
            el efecto, o en su defecto, la quinta parte de lo que se estime para la
            concreci&oacute;n de la obra o labor contratada.
            <strong
            ><u
                >SEXTA. JUSTAS CAUSAS PARA DAR POR TERMINADO LA RELACION CONTRACTUAL:</u
            ></strong
            >
            Son justas causas para dar por terminado unilateralmente el presente
            contrato por cualquiera de las partes, como el incumplimiento de las
            obligaciones y prohibiciones que se expresan en los art&iacute;culos 57 y
            siguientes del c&oacute;digo sustantivo del trabajo. Adem&aacute;s, el
            incumplimiento o violaci&oacute;n de las normas establecidas en este
            contrato, en el reglamento interno de trabajo, en el programa de salud
            ocupacional y los reglamentos de higiene y seguridad, adem&aacute;s las
            previamente establecidas por el empleador o sus representantes y en especial
            las siguientes:
            <strong>1.</strong> Paralizar temporal y permanentemente la labor para la
            cual ha sido contratado y el cual se considerar&aacute; falta grave y justa
            causa para terminar el v&iacute;nculo contractual. <strong>2.</strong> La
            violaci&oacute;n por parte del trabajador de cualquiera de sus obligaciones,
            deberes y prohibiciones legales, contractuales o reglamentarias, a&uacute;n
            por la primera vez. <strong>3.</strong> La no asistencia puntual al trabajo,
            sin excusa suficiente a juicio del empleador, a&uacute;n por la primera vez.
            <strong>4. </strong>La ejecuci&oacute;n por parte del trabajador de labores
            remuneradas a servicio de terceros sin autorizaci&oacute;n del empleador,
            a&uacute;n por la primera vez. <strong>5. </strong>La revelaci&oacute;n de
            secretos y datos reservados de la empresa, a&uacute;n por la primera vez.
            <strong>6. </strong>Las desavenencias con sus compa&ntilde;eros de trabajo,
            a&uacute;n por la primera vez. <strong>7. </strong>El hecho de que EL
            TRABAJADOR llegue embriagado o en estado de alicoramiento al trabajo o que
            ingiera bebidas embriagantes en el sitio de trabajo, aun por la primera vez.
            <strong>8. </strong>El hecho que EL TRABAJADOR suspenda la labor sin
            autorizaci&oacute;n, a&uacute;n por la primera vez &oacute; abandone el
            sitio de trabajo o el lugar donde deba cumplir con sus funciones sin el
            permiso de sus superiores, a&uacute;n por la primera vez.
            <strong>9. </strong>La no asistencia a una sesi&oacute;n completa de la
            jornada de trabajo, o m&aacute;s, sin excusa suficiente a juicio del
            empleador, salvo fuerza mayor o caso fortuito, a&uacute;n por la primera
            vez. <strong>10. </strong>No aceptar el cambio de turno o lugar de trabajo
            dentro y fuera de la empresa sin razones v&aacute;lidas expuestas por el
            empleador, a&uacute;n por la primera vez. <strong>11. </strong>Amenazar a
            sus compa&ntilde;eros de trabajo, superiores y/o clientes de la empresa,
            a&uacute;n por la primera vez. <strong>12. </strong>El usar, vender,
            distribuir y/o portar drogas alucin&oacute;genas o estupefacientes en el
            sitio de trabajo, a&uacute;n por la primera vez. <strong>13. </strong>El
            utilizar el nombre de la empresa para obtener cualquier tipo de provecho
            para s&iacute;, para sus parientes o allegados, a&uacute;n por la primera
            vez. <strong>14. </strong>El calumniar, injuriar, pelear e irrespetar de
            palabra o de obra, dentro o fuera del sitio de trabajo con sus
            compa&ntilde;eros de trabajo, superiores o familiares de &eacute;ste,
            a&uacute;n por la primera vez. <strong>14. </strong>Cualquier
            informaci&oacute;n falsa suministrada a la empresa, a&uacute;n por la
            primera vez. <strong>15. </strong>El sustraer implementos de trabajo o
            insumos que se utilizan en la empresa sin la autorizaci&oacute;n previa y
            escrita, a&uacute;n por la primera vez. <strong>16. </strong>No presentar
            los reportes de trabajo en el momento indicado por la empresa, a&uacute;n
            por la primera vez. <strong>17. </strong>No cumplir con la labor programada,
            a&uacute;n por la primera vez. <strong>18. </strong>Entregar
            informaci&oacute;n o documentaci&oacute;n incompleta o retardada, a&uacute;n
            por la primera vez. <strong>19. </strong>Deteriorar, perder o da&ntilde;ar
            los activos asignados para el desarrollo de sus labores, a&uacute;n por la
            primera vez. <strong>20. </strong>Detener un trabajo por falta de
            informaci&oacute;n y no comunicarlo. <strong>21. </strong>El hacer caso
            omiso a las llamadas de atenci&oacute;n por ineficiencia en la labor,
            a&uacute;n por la primera vez. <strong>22. </strong>Entorpecer las tareas de
            sus compa&ntilde;eros, a&uacute;n por la primera vez.
            <strong>23. </strong>Extraer informaci&oacute;n de la empresa (clientes,
            productos, trabajos etc.) para ser utilizada en provecho personal o de la
            competencia, a&uacute;n por la primera vez. <strong>24. </strong>El realizar
            trabajos para provecho o uso personal sin autorizaci&oacute;n de la empresa,
            a&uacute;n por la primera vez. <strong>25. </strong>Desacatar o menos
            preciar las sugerencias ofrecidas por sus superiores.
            <strong>26. </strong>Realizar rifas o negocios personales dentro de la
            empresa, a&uacute;n por la primera vez. <strong>27. </strong>La
            p&eacute;rdida de herramientas o implementos de trabajo dados a su cargo y
            bajo su responsabilidad y cuidado, a&uacute;n por la primera vez.
            <strong>28. </strong>Cualquier violaci&oacute;n de las obligaciones, deberes
            y prohibiciones pactadas en este contrato, as&iacute; como tambi&eacute;n de
            los se&ntilde;alados en la cl&aacute;usula especial de este documento,
            a&uacute;n por la primera. <strong>29. </strong>La terminaci&oacute;n total
            o parcial del contrato de servicios personales o especializados por parte
            del cliente o usuario del EMPLEADOR, el cual terminara inmediatamente la
            obra o la labor determinada para la cual ha sido contratado El TRABAJADOR,
            entendi&eacute;ndose que el objeto y el fin del contrato suscrito por el
            TRABAJADOR Y EL EMPLEADOR ha dejado de existir en la vida jur&iacute;dica.
            <strong><u>PAR&Aacute;GRAFO PRIMERO. INDEMNIZACION DE PERJUICIOS</u></strong
            >. En caso que EL TRABAJADOR incurra en cualquier conducta descrita en el
            presente contrato, como el incumplimiento de las obligaciones y
            prohibiciones que se expresan en los art&iacute;culos 57 y siguientes del
            c&oacute;digo sustantivo del trabajo, adem&aacute;s el incumplimiento o
            violaci&oacute;n de las normas establecidas en el reglamento interno de
            trabajo, higiene y seguridad y las previamente establecidas por el empleador
            o sus representantes y en el especial lo consagrado en el numeral 1 y 2 de
            la presente clausula y que de esta par&aacute;lisis temporal o permanente de
            la labor para cual ha sido contratado, genere da&ntilde;os y perjuicios a
            los clientes, usuarios, socios, accionistas o representantes de la
            compa&ntilde;&iacute;a, El EMPLEADOR tendr&aacute; derecho a exigir al
            TRABAJADOR el pago de indemnizaci&oacute;n por perjuicios, da&ntilde;o
            emergente y lucro cesante, de acuerdo con el Art&iacute;culo 1600 del
            C&oacute;digo Civil Colombiano.
            <strong><u>PAR&Aacute;GRAFO SEGUNDO.</u></strong
            ><strong
            ><u>LIQUIDACI&Oacute;N Y ENTREGA DE ACREENCIAS LABORALES.</u></strong
            >
            En consideraci&oacute;n a que toda liquidaci&oacute;n y pago de salarios y
            prestaciones sociales exige varios d&iacute;as para obtener los datos e
            informes necesarios y as&iacute; mismo, implica su revisi&oacute;n,
            realizar, aprobar la liquidaci&oacute;n definitiva constituir la partida de
            pago correspondiente. Las partes de com&uacute;n acuerdo, aclaran que existe
            un tiempo prudencial y razonable para estos efectos, tal como en su
            oportunidad lo manifiesto la Corte Suprema de Justicia. Dentro de este plazo
            prudencial y de buena fe EL EMPLEADOR podr&aacute; liquidar y pagar los
            salarios y/o prestaciones sociales sin que por ello incurra en mora de
            ninguna naturaleza ni quede obligado a pagar la indemnizaci&oacute;n
            moratoria contemplada de la ley 789 de 2002. De igual manera se deja expresa
            constancia de la especial obligaci&oacute;n del TRABAJADOR de tramitar
            directamente su paz y salvo empresarial, documento sin el cual no se pueden
            tramitar las acreencias laborales que le correspondan y despu&eacute;s del
            tiempo prudencial del caso, se proceder&aacute; a la consignaci&oacute;n en
            el banco Agrario de Colombia en una cuenta de dep&oacute;sito judicial.
            <strong
            ><u>SEPTIMA.- NUEVA OBRA O CAMBIO DEL TERMINO DEL CONTRATO</u>: </strong
            >Si al finalizar la obra o la labor contratada, El EMPLEADOR desea continuar
            con El TRABAJADOR en otra obra o labor distinta a la aqu&iacute; contratada,
            EL EMPLEADOR y TRABAJADOR dar&aacute;n por terminado por mutuo acuerdo el
            presente contrato, oblig&aacute;ndose el empleador a cancelar al trabajador,
            lo correspondiente a la liquidaci&oacute;n de prestaciones sociales.
            Despu&eacute;s de realizado lo anterior, el empleador y el trabajador
            proceder&aacute;n a realizar un nuevo contrato por el termino de
            duraci&oacute;n seg&uacute;n lo pacten las partes.
            <strong><u>OCTAVA. ESIPULACION SALARIAL:</u></strong> EL EMPLEADOR
            cancelar&aacute; al TRABAJADOR por concepto de salario, lo estipulado en el
            recuadro inicial. Los tiempos de pago del salario ser&aacute;n los
            establecidos en el recuadro inicial y se realizar&aacute; mediante
            transacci&oacute;n electr&oacute;nica, para lo cual EL TRABAJADOR debe hacer
            apertura de cuenta de n&oacute;mina una vez se firme el presente contrato, o
            en caso que no sea posible lo relativa a la cuenta bancaria,
            excepcionalmente se pagar&aacute; directamente en efectivo.
            <strong><u>NOVENA. LUGAR DE PRESTACI&Oacute;N DEL SERVICIO.</u></strong>
            EL TRABAJADOR desarrollar&aacute; sus funciones en el Municipio de
            Gir&oacute;n (Santander); sin perjuicio de que el EMPLEADOR requiera del
            traslado del TRABAJADOR para lo cual, &eacute;ste &uacute;ltimo acepta con
            la suscripci&oacute;n del presente contrato, cualquier cambio de lugar de
            trabajo siempre y cuando no desmejore sus condiciones de vida.
            <strong><u>DECIMA.- MODIFICACIONES.</u></strong> El presente contrato
            remplaza en su integridad y deja sin efecto alguno cualquiera otro contrato
            verbal o escrito celebrado entre las partes con anterioridad. Las
            modificaciones que se acuerden al presente contrato se anotar&aacute;n a
            continuaci&oacute;n de su texto.
            <strong><u>DECIMA PRIMERA.- CLAUSULA DE EXCLUSIVIDAD:</u></strong> durante
            la vigencia de este acuerdo, EL TRABAJADOR se compromete formalmente a
            trabajar exclusivamente para EL EMPLEADOR y no puede ejercer ninguna otra
            funci&oacute;n en cualquier otra empresa a t&iacute;tulo que sea en el caso
            de cese por los motivos que fueran, se compromete a no ofrecer sus servicios
            directa o indirectamente a cualquier empresa, o cliente, para realizar la
            misma actividad que desempe&ntilde;a con el cliente. Entre EL TRABAJADOR y
            EL EMPLEADOR existe exclusividad laboral. El EMPLEADOR es el &uacute;nico
            responsable del pago de salarios, prestaciones sociales, indemnizaciones, y
            dem&aacute;s emolumentos a los cuales tenga derecho el trabajador.
            <strong><u>D&Eacute;CIMA SEGUNDA</u>: </strong>El TRABAJADOR mediante este
            contrato declara que ha le&iacute;do, entiende y acepta el contenido de este
            contrato, as&iacute; como tambi&eacute;n el del reglamento interno de
            trabajo vigente en la empresa. <br><br> Para constancia se firma en Gir&oacute;n
            (Santander), a los ' . $diaenletras . ' ('. $dia . ') d&iacute;as del mes de '. $mes .' de '.$year.'.
            <table style="width: 100%; margin-top: 30px;">
                <thead style="text-align: left;">
                    <tr style="text-align: left;">
                        <th style="text-align: left;"><strong>EL EMPLEADOR</strong></th>
                        <th style="text-align: left;"><strong>EL EMPLEADO</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-top: 40px;">____________________________________</td>
                        <td style="padding-top: 40px;">____________________________________</td>
                    </tr>
                    <tr>
                        <td><strong>ALBERTO BALCARCEL ZAMBRANO </strong></td>
                        <td><strong>' . $funcionario->first_name . ' ' . $funcionario->second_name . ' ' . $funcionario->first_surname . ' ' . $funcionario->second_surname . '</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Representante Legal </strong></td>
                        <td><strong>C.C. '. $funcionario->identifier .'</strong></td>
                    </tr>
                    <tr>
                        <td><strong>MAQUINADOS &amp; MONTAJES S.A.S.</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>

  ';
        if ($contract->work_contract_type_id == 1) {
            $contenido = 'Indefinido';
        } else if ($contract->work_contract_type_id == 2) {
            $contenido = $contenifoFijo;
        } else if ($contract->work_contract_type_id == 3) {
            $contenido = $contenidoObraLaboral;
        } 
        $pdf = PDF::loadHtml($contenido);
        return $pdf->download('contrato.pdf');
    }
}
