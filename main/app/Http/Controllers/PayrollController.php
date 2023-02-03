<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 180); //3 minutes

use App\Http\Libs\Nomina\Calculos\ResumenPago;
use App\Http\Libs\Nomina\Facades\NominaDeducciones;
use App\Http\Libs\Nomina\Facades\NominaIngresos;
use App\Http\Libs\Nomina\Facades\NominaNovedades;
use App\Http\Libs\Nomina\Facades\NominaPago;
use App\Http\Libs\Nomina\Facades\NominaProvisiones;
use App\Http\Libs\Nomina\Facades\NominaRetenciones;
use App\Http\Libs\Nomina\Facades\NominaSalario;
use App\Http\Libs\Nomina\Facades\NominaSeguridad;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\ElectronicPayroll;
use App\Models\Payroll;
use App\Models\PayrollOvertime;
use App\Models\PayrollPayment;
use App\Models\PayrollSocialSecurityPerson;
use App\Models\Person;
use App\Models\PersonPayrollPayment;
use App\Services\PayrollReport;
use App\Services\PayrollService;
use App\Traits\ApiResponser;
use App\Traits\ElectronicDian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PayrollController extends Controller
{
    use ApiResponser;
    use ElectronicDian;

    //
    /**
     * Reporta a la dian
     *
     * @return Json
     */

    public function reportDian($id, $idPersonPayroll = null)
    {
        $payroll = PayrollPayment::with('company')
            ->with('company.configuration')
            ->with('company.payConfiguration')
            ->where('id', $id)->first();

        $poplePay = PersonPayrollPayment::where('payroll_payment_id', $id)
            ->whereNull('electronic_reported')
            ->when($idPersonPayroll, function ($q, $fill) {
                $q->where('id', $fill);;
            })
            ->get();


        $poplePay->each(function ($personPay) use ($payroll) {
            $config = Configuration::where('company_id', $payroll->company->id)->first();
            $configData =  $config->consecutivoLevel1('Nomina', 'Nomina');

            $data = [];
            $data['type_document_id'] = 7;
            $data['resolution_number'] = 2;
            $data['resolution_number'] = 7;
            $data['date_pay'] =  date("Y-m-d", strtotime($payroll->created_at));

            $data['payroll_period'] = $payroll->payment_frequency;
            $data['date'] = date("Y-m-d", strtotime($payroll->created_at));

            $data['observation'] = 'nomina electronica';

            $data['prefix'] = $configData->prefix;
            $data['number'] = $configData->number;
            $data['code'] = $configData->code;
            $data['file'] = $configData->code;

            /*      date("H:i:s", strtotime(No));
             */
            $data['date_start_period'] = $payroll->start_period;
            $data['date_end_period'] = $payroll->end_period;

            $data['hour'] = date("H:i:s", strtotime($payroll->created_at)) . '-05:00';

            // 16:53:23
            // 10:45:33-05:00
            //  dd($data['hour']);
            $person = [];

            $workContract = $personPay->person->contractultimate;
            if (!$workContract->reported_integraition_dian) {
                $data['integration_date'] = date("Y-m-d", strtotime($workContract->date_of_admission));

                $person['contractultimate'] = 1;
                /* TODO activar */
                //$workContract->contractultimate->save();
            }

            $person['historic_worked_time'] = PayrollReport::calculateWorkedDays($workContract->date_of_admission, $payroll->end_period);
            $person['salary'] = $workContract->salary;
            $person['code'] =  $personPay->person->identifier;

            /* TODO salario integral quemado */
            $person['salary_integral'] = "true";
            $person['worker_type'] = [];
            $person['worker_type']['code'] = $workContract->worker_type_dian_code;

            /* TODO salario subtipo de salario */
            $person['worker_subtype'] = [];
            $person['worker_subtype']['code'] = "00";

            $person['work_contract_type'] = [];
            /*   $tipoContrato =  $workContract->work_contract_type;*/
            $person['work_contract_type']['code'] = $workContract->work_contract_type->dian_code;



            $person['high_risk_pension'] = "false";
            $person['identifier'] = $personPay->person->identifier;
            $person['first_name'] = $personPay->person->first_name;
            $person['middle_name'] = $personPay->person->second_name;
            $person['last_name'] = $personPay->person->first_surname;
            $person['last_names'] = $personPay->person->second_surname;

            $person['type_document_identification'] = [];

            $person['type_document_identification']['code'] = $personPay->person->documentType->dian_code;

            $person['work_place'] = [];
            $person['work_place']['country'] = [];
            $person['work_place']['country']['code'] = "CO";

            /*   dd($person); */

            /* TODO monicipio quemado */
            $person['work_place']['municipality'] = [];

            $person['work_place']['municipality']['department'] = [];

            $person['work_place']['country']['code'] = "CO";

            $person['work_place']['municipality']['code'] = "05001";
            $person['work_place']['municipality']['department']['code'] = "05";

            $person['work_place']['addres'] = $personPay->person->address;

            $data['person'] = $person;

            $pay = [];
            $pay['payroll_pay_formate'] = [];
            $pay['payroll_pay_formate']['code'] = 1;

            $pay['payroll_pay_method'] = [];
            $pay['payroll_pay_method']['code'] = 1;

            $data['pay'] = $pay;

            $accrued = [];
            $accrued['basic'] = [];
            $accrued['basic']['worked_days'] = $personPay->worked_days;
            $accrued['basic']['salary_payroll'] = $personPay->net_salary;


            if ($payroll->company->payConfiguration->pay_transportation_assistance) {

                $accrued['transport_subsidy'] = [];
                $accrued['transport_subsidy']['salarial'] = $payroll->company->transportation_assistance;
            }

            /* Incapacidades */



            /* TODO guardar consecutivo
                $confg->savePrefix();
            */
            $factors = DB::table('payroll_factors as p')
                ->join('disability_leaves as d', 'd.id', 'p.disability_leave_id')
                ->where('person_id', $personPay->person->id)
                ->whereDate('p.date_start', '>=', DB::raw("date('$payroll->start_period')"))
                ->whereDate('p.date_end', '<=', DB::raw("date('$payroll->end_period')"))
                ->select('p.*', 'd.code_dian', 'd.maternity', 'd.not_paid_license', 'd.paid_license')
                ->get();



            $inhabilities = [];
            $licencess = [];
            $licencess['mp'] = [];
            $licencess['r'] = [];
            $licencess['nr'] = [];

            $factors->each(function ($x) use ($accrued, &$inhabilities, &$licencess, $personPay) {

                $data = [];
                $data['date_start'] = Carbon::parse($x->date_start)->format('Y-m-d');
                $data['date_end'] =  Carbon::parse($x->date_end)->format('Y-m-d');
                $data['days'] =  ($x->number_days + 1);
                $data['type'] =  $x->code_dian;
                $data['value'] = ($personPay->person->contractultimate->salary * ($x->number_days + 1)) / 30;


                if ($x->disability_type == 'Incapacidad') {
                    $inhabilities[] = $data;
                }
                if ($x->disability_type == 'Licencia' &&  $x->maternity == 1) {
                    $licencess['mp'][] = $data;
                }
                if ($x->disability_type == 'Licencia' &&  $x->not_paid_license == 1) {
                    $licencess['nr'][] = $data;
                    unset($data['value']);
                }
                if ($x->disability_type == 'Licencia' &&  $x->paid_license == 1) {
                    $licencess['r'][] = $data;
                }
            });


            /*         count($licencess->mp) == 0 ?  unset($licencess->mp) : ''; */

            $accrued['inability'] =  $inhabilities;
            $accrued['licences'] =  $licencess;

            $bonus = [];
            $assistances = [];
            $others = [];
            $commissions = [];

            $countableNotIncomes = DB::table('benefit_not_incomes as n')
                ->join('countable_income as c', 'c.id', 'n.countable_income_id')
                ->where('person_id', $personPay->person->id)
                ->whereDate('n.created_at', '>=', DB::raw("date('$payroll->start_period')"))
                ->whereDate('n.created_at', '<=', DB::raw("date('$payroll->end_period')"))
                ->select('n.*', 'c.type',  'c.bonus', 'c.assistence', 'c.others', 'c.commission')
                ->get();

            $countableIncomes = DB::table('benefit_incomes as n')
                ->join('countable_income as c', 'c.id', 'n.countable_income_id')
                ->where('person_id', $personPay->person->id)
                ->whereDate('n.created_at', '>=', DB::raw("date('$payroll->start_period')"))
                ->whereDate('n.created_at', '<=', DB::raw("date('$payroll->end_period')"))
                ->select('n.*', 'c.type',  'c.bonus', 'c.assistence', 'c.others', 'c.commission')
                ->get();


            //dd($countableIncomes);

            $countableNotIncomes->each(function ($x) use (&$bonus, &$assistances, &$others, &$commissions) {
                if ($x->bonus == 1) {
                    $bonus[] = ['no_salarial' => $x->value];
                }
                if ($x->assistence == 1) {
                    $assistances[] = ['no_salarial' => $x->value];
                }

                if ($x->others == 1) {
                    $others[] = ['no_salarial' => $x->value, 'description' => 'Otra deducción'];
                }
                if ($x->commission == 1) {
                    $commissions[] = ['value' => $x->value];
                }
            });

            $countableIncomes->each(function ($x) use (&$bonus, &$assistances, &$others, &$commissions) {
                if ($x->bonus == 1) {
                    $bonus[] = ['salarial' => $x->value];
                }

                if ($x->assistence == 1) {
                    $assistances[] = ['salarial' => $x->value];
                }
                if ($x->others == 1) {
                    $others[] = ['salarial' => $x->value, 'description' => 'Otra deducción'];
                }
                if ($x->commission == 1) {
                    $commissions[] = ['value' => $x->value];
                }
            });

            $accrued['inability'] =  $inhabilities;
            $accrued['licences'] =  $licencess;

            $bonus  ? $accrued['bonus'] = $bonus : null;
            $assistances  ? $accrued['assistances'] = $assistances : null;
            $others  ? $accrued['others'] = $others : null;
            $commissions  ? $accrued['commissions'] = $commissions : null;

            $data['accrued'] = $accrued;



            /* deductions */

            $deductions = [];

            $deductions['healt'] = [];
            $deductions['pension_funds'] = [];

            $retDed = $this->getRetenciones($personPay->person->id, $payroll->start_period, $payroll->end_period);




            $deductions['healt']['deduction'] = $retDed['total_retenciones']['Salud'];
            $deductions['healt']['percentage'] = $retDed['porcentajes']['Salud'] * 100;

            $deductions['pension_funds']['deduction'] = $retDed['total_retenciones']['Pensión'];
            $deductions['pension_funds']['percentage'] = $retDed['porcentajes']['Pensión'] * 100; /**/


            $deductionsDB = DB::table('deductions as n')
                ->join('countable_deductions as cd', 'cd.id', 'n.countable_deduction_id')
                ->where('n.person_id', $personPay->person->id)
                ->whereDate('n.created_at', '>=', DB::raw("date('$payroll->start_period')"))
                ->whereDate('n.created_at', '<=', DB::raw("date('$payroll->end_period')"))
                ->select('n.value', 'cd.loan', 'cd.others')
                ->get();


            /*  $loan
           */
            $loans = [];
            $other_deductions = [];
            $deductionsDB->each(function ($x) use (&$loans, &$other_deductions) {
                if ($x->loan == 1) {
                    $x->description = "prestamo cuota";
                    $loans[] = $x;
                }
                if ($x->others == 1) {
                    $other_deductions[] = $x;
                }
            });

            $deductions['loans'] = $loans;
            $deductions['other_deductions'] = $other_deductions;

            $data['deductions'] = $deductions;

            /*  */

            $novedadesTotal = $this->getNovedades($personPay->person, $payroll->start_period, $payroll->end_period)['valor_total'];
            $ingresosAdicionalesTotal = $this->getIngresos($personPay->person, $payroll->start_period, $payroll->end_period)['valor_total'];

            $extrasTotal = $this->getExtrasTotales($personPay->person, $payroll->start_period, $payroll->end_period)['valor_total'];

            $deduccionesTotal = $this->getDeducciones($personPay->person, $payroll->start_period, $payroll->end_period)['valor_total'];
            $retencionTotal = $this->getRetenciones($personPay->person->id, $payroll->start_period, $payroll->end_period)['valor_total'];

            $salario = $this->getPagoNeto($personPay->person, $payroll->start_period, $payroll->end_period)['total_valor_neto'];


            $totals = [];

            $totals['accrued'] = number_format($salario + ($deduccionesTotal + $retencionTotal), 2, '.', '');

            $totals['deductions'] = number_format($deduccionesTotal + $retencionTotal, 2, '.', '');

            $totals['voucher'] = number_format($salario, 2, '.', '');


            $data['totals'] = $totals;


            $data['cune_propio'] = $this->cuneGenerate($data, $payroll->company, '00777', '102', 1);

            $responseDian =  $this->sendElectronicPayroll($data, $payroll);

            $payrollElectronic = new ElectronicPayroll();
            $payrollElectronic->status = $responseDian['status'];
            $payrollElectronic->message = $responseDian['message'];
            $payrollElectronic->errors = $responseDian['errors'];
            $payrollElectronic->person_payroll_payment_id = $personPay->id;
            $payrollElectronic->cune = $responseDian['status'] == 'succeded' ?  $data['cune_propio'] : '';
            $payrollElectronic->code = $responseDian['status'] == 'succeded' ?  $data['code'] : '';
            $payrollElectronic->save();

            $personPay->status = $responseDian['status'];


            if ($responseDian['status'] == 'succeded') {
                $personPay->code = $payrollElectronic->code;
                $personPay->cune = $payrollElectronic->cune;

                $personPay->electronic_reported = 1;
                $personPay->electronic_reported_date = $data['date'];
                $personPay->user_electronic_reported = auth()->user()->id;
                $config->savePrefix('Nomina');
            }

            $personPay->save();



            /* -------- */
            /* dd($deductions,$); */
        });

        return $this->success('Reportado correctamente');
    }

    /*  public function store(PagosNominaStoreRequest $pagosNominaStoreRequest) */
    public function store()
    {

        try {

            $atributos = request()->all();

            $atributos['start_period'] = Carbon::parse(request()->get('start_period'))->format('Y-m-d');
            $atributos['end_period'] =  Carbon::parse(request()->get('end_period'))->format('Y-m-d');

            $atributos['payment_frequency'] = Company::get(['payment_frequency'])->first()['payment_frequency'];

            $funcionarios = Person::whereHas('contractultimate', function ($query) use ($atributos) {
                return $query->whereDate('date_of_admission', '<=', $atributos['end_period'])->whereDate('date_end', '>=', $atributos['start_period'])
                    ->orWhereNull('date_end')
                    ->where('liquidated', '0');;
            })->where('status', '!=', 'Liquidado')->get();
            $atributos['code'] = generateConsecutive('payroll_payments');
            $pagoNomina = PayrollPayment::create($atributos);
            sumConsecutive('payroll_payments');
            $funcionarios->each(function ($funcionario) use ($pagoNomina) {
                $code_person = generateConsecutive('person_payroll_payments');
                $salario =  $this->getSalario($funcionario, $pagoNomina->start_period, $pagoNomina->end_period);
                $pagoNomina->personPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'worked_days' => $salario['worked_days'],
                    'salary' => $salario['salary'],
                    'code' => $code_person,
                    'transportation_assistance' => $salario['transportation_assistance'],
                    'retentions_deductions' => $this->getRetenciones($funcionario, $pagoNomina->start_period, $pagoNomina->end_period)['valor_total'],
                    'net_salary' => $this->getPagoNeto($funcionario, $pagoNomina->start_period, $pagoNomina->end_period)['total_valor_neto'],
                ]);

                $previsiones =  $this->getProvisiones($funcionario, $pagoNomina->start_period, $pagoNomina->end_period);


                $pagoNomina->provisionsPersonPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'severance' =>  $previsiones['resumen']['cesantias']['valor'],
                    'severance_interest' =>  $previsiones['resumen']['intereses_cesantias']['valor'],
                    'prima' =>  $previsiones['resumen']['prima']['valor'],
                    'vacations' =>  $previsiones['resumen']['vacaciones']['valor'],
                    'accumulated_vacations' =>   $previsiones['dias_vacaciones']['vacaciones_acumuladas_periodo'],
                    'total_provisions' =>  $previsiones['valor_total']
                ]);

                $seguridad =  $this->getSeguridad($funcionario, $pagoNomina->start_period, $pagoNomina->end_period);

                $pagoNomina->socialSecurityPersonPayrollPayment()->create([
                    'person_id' => $funcionario->id,
                    'payroll_payment_id' => $pagoNomina->id,
                    'health' =>  $seguridad['seguridad_social']['Salud'],
                    'pension' =>  $seguridad['seguridad_social']['Pensión'],
                    'risks' =>  $seguridad['seguridad_social']['Riesgos'],
                    'sena' => $seguridad['parafiscales']['Sena'],
                    'icbf' => $seguridad['parafiscales']['Icbf'],
                    'compensation_founds' => $seguridad['parafiscales']['Caja de compensación'],
                    'total_social_security' => $seguridad['valor_total_seguridad'],
                    'total_parafiscals' => $seguridad['valor_total_parafiscales'],
                    'total_social_security_parafiscals' => $seguridad['valor_total'],
                ]);
                sumConsecutive('person_payroll_payments');
            });

            return $this->success('Nómina guardada correctamente',  $pagoNomina);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->errorResponse($th->getMessage(),  500);
        }
    }

    public function getFuncionario($identidad)
    {
        $funcionario = Person::where('id', '=', $identidad)->with('contractultimate')->first();
        if (!$funcionario) {
            return response()->json(['message' => 'Funcionario no encontrado'], 404);
        }

        return $funcionario;
    }

    function nextMonths()
    {
        try {
            return $this->success(PayrollService::getQuincena());
        } catch (\Throwable $th) {
            return $this->error($th->getMessage() . $th->getLine(), 402);
        }
    }

    /**
     * Retorna el resumen del pago de nómina en el mes actual, si ya existe en la BD entonces se modifica la  respuesta agregando propiedades a la respuesta
     *
     * @return Json
     */
    /**
     * Instancia clase ResumenPago
     *
     * @var ResumenPago
     */
    protected $salario;

    public function getPayrollPay($inicio = null, $fin = null)
    {

        $current = !$inicio ? true : false;
        $frecuenciaPago =  Company::get(['payment_frequency'])->first()['payment_frequency'];
        $pagoNomina = $nomina = $paga = $idNominaExistente = null;
        $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
        $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");

        $totalSalarios = 0;
        $totalRetenciones = 0;
        $totalSeguridadSocial = 0;
        $totalParafiscales = 0;
        $totalProvisiones = 0;
        $totalExtras = 0;
        $totalIngresos = 0;
        $totalCostoEmpresa = 0;

        if ($frecuenciaPago === 'Quincenal') {
            if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                $fechaInicioPeriodo = date("Y-m-16 00:00:00");
            } else {
                $fechaFinPeriodo = date("Y-m-15 23:59:59");
            }
        }

        $fechaInicioPeriodo = $fechaInicioPeriodo;
        $fechaFinPeriodo = $fechaFinPeriodo;


        /**
         * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
         * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
         * desde el componente de historial de pagos en Vue cuando se realiza una petición.
         */
        if ($inicio) {
            $fechaInicioPeriodo = $inicio;
        }

        if ($fin) {
            $fechaFinPeriodo = $fin;
        }


        /**
         * Comprobar si ya existe un pago de nómina en el periodo
         */
        if ($current) {
            # code...
            $nomina = PayrollPayment::latest()->first();
        } else {
            $nomina = PayrollPayment::whereDate('start_period', $fechaInicioPeriodo)
                ->whereDate('end_period', $fechaFinPeriodo)
                ->first();
        }
        if ($nomina) {
            $idNominaExistente = $nomina->id;
            $paga = $current ? Carbon::now()->between($nomina->start_period, $nomina->end_period) : true;
        }


        $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
        };

        $funcionarios = Person::with('contractultimate')->with('work_contract')->whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)
                ->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end')
                ->where('liquidated', '0');
        })->with(['payroll_factors' => $fechasNovedades])->get();

/*         $funcionarios = Person::with('contractultimate')->with('work_contract')->whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
            return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)
            ->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end')
            ->where('liquidated', '0');
        })->with(['payroll_factors' => $fechasNovedades])->get(['id', 'identifier', 'first_name', 'first_surname', 'image']); */
        try {

            $funcionariosResponse = [];
            foreach ($funcionarios as $funcionario1) {
                $funcionario = Person::find($funcionario1->id);

                //cálculos que no dependen de otro
                $temIngresos = $this->getIngresos($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo);
                $tempNovedades = $this->getNovedades($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo);
                $tempDeducciones = $this->getDeducciones($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo);

                $salarioBase = NominaSalario::salarioFuncionarioWithPerson($funcionario)->fromTo($fechaInicioPeriodo, $fechaFinPeriodo)->calculate();

                $tempExtras = $this->getExtrasTotales($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo);
                $extras = $tempExtras['valor_total'];

                //$retencion = $this->getRetenciones($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
                $retencion = NominaRetenciones::retencionesFuncionarioWithPerson($funcionario)
                    ->withParams($salarioBase, $tempExtras, $tempNovedades, $temIngresos, $fechaInicioPeriodo, $fechaFinPeriodo)
                    ->calculate();

                //$tempSeguridad = $this->getSeguridad($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo);
                $tempSeguridad = NominaSeguridad::seguridadFuncionarioWithPerson($funcionario)
                        ->withParams($retencion, $tempNovedades, $fechaInicioPeriodo, $fechaFinPeriodo)
                        ->calculate();

                $seguridad = $tempSeguridad['valor_total_seguridad'];
                $parafiscal = $tempSeguridad['valor_total_parafiscales'];

                //$provision = $this->getProvisiones($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'];
                $provision = NominaProvisiones::provisionesFuncionarioWithPerson($funcionario)
                    ->withParams($salarioBase, $tempExtras, $tempNovedades, $retencion, $fechaInicioPeriodo, $fechaFinPeriodo)
                    ->calculate();


                /***Lo siguiente evita la funcion getPagoNeto para evitar
                 * que use muchos de los facades Nominas ya empleados anteriormente
                 **/
                //$salario = $this->getPagoNeto($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'];
                $code = PersonPayrollPayment::where('person_id', $funcionario->id)->select('code')->latest()->first();
                $salario = NominaPago::pagoFuncionarioWithPerson($funcionario)
                        ->withParams($salarioBase, $tempExtras, $tempNovedades, $temIngresos, $retencion, $tempDeducciones, $fechaInicioPeriodo, $fechaFinPeriodo)
                        ->calculate();
                //dd($temIngresos['valor_total']);

                //$totalSalarios +=  $salario['total_valor_neto']; //que no incluya horas extras ni ingresos adicionales
                //dd($salarioBase['salary']);
                //$totalSalarios +=  $salarioBase['salary'];
                $totalSalarios +=  $salario['total_valor_neto'];
                $totalRetenciones += $retencion['valor_total'];
                $totalSeguridadSocial += $seguridad;
                $totalParafiscales += $parafiscal;
                $totalProvisiones += $provision['valor_total'];
                $totalExtras += $extras;
                $totalIngresos += $temIngresos['valor_total'];

                $funcionariosResponse[] = [
                    'id' => $funcionario->id,
                    'identifier' => $funcionario->identifier,
                    'name' => $funcionario->first_name,
                    'surname' => $funcionario->first_surname,
                    'image' => $funcionario->image,
                    'salario_neto' => $salario['total_valor_neto'],
                    //'salario_neto' => $salario,
                    'novedades' => [],
                    'code' => $code && $code->code ? $code->code : '',
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $tempExtras['horas_reportadas'],
                    'novedades' => $tempNovedades['novedades'],
                    'valor_ingresos_salariales' => ($temIngresos['valor_constitutivos'] +
                        //$tempNovedades['valor_total'] +
                        $extras),
                    'valor_ingresos_no_salariales' => $temIngresos['valor_no_constitutivos'],
                    'valor_deducciones' => $tempDeducciones['valor_total']
                ];
            }

//dd($totalIngresos);

            $totalCostoEmpresa += $totalSalarios + $totalRetenciones +   $totalSeguridadSocial + $totalParafiscales + $totalProvisiones;


            return $this->success([
                'frecuencia_pago' => $frecuenciaPago,
                'inicio_periodo' => $fechaInicioPeriodo,
                'fin_periodo' => $fechaFinPeriodo,
                'salarios' =>  $totalSalarios,
                'seguridad_social' => $totalSeguridadSocial,
                'parafiscales' => $totalParafiscales,
                'provisiones' => $totalProvisiones,
                'extras' => $totalExtras,
                'ingresos' => $totalIngresos,
                'retenciones' => $totalRetenciones,
                'costo_total_empresa' => $totalCostoEmpresa,
                'nomina_paga' => $paga,
                'nomina' => $nomina,
                'nomina_paga_id' => $idNominaExistente,
                'code' => $nomina && $nomina->code ? $nomina->code : '',
                'total_funcionarios' => count($funcionariosResponse),
                'funcionarios' => $funcionariosResponse
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return [$th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
        }
    }

    public function payPeople($inicio = null, $fin = null)
    {
        try {

            $frecuenciaPago =  Company::get(['payment_frequency'])->first()['payment_frequency'];

            $fechaInicioPeriodo = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
            $fechaFinPeriodo = Carbon::now()->endOfMonth()->format("Y-m-d H:i:s");
            $funcionariosResponse = [];

            if ($frecuenciaPago === 'Quincenal') {
                if (date("Y-m-d") > date("Y-m-15 00:00:00")) {
                    $fechaInicioPeriodo = date("Y-m-16 00:00:00");
                } else {
                    $fechaFinPeriodo =     date("Y-m-15 23:59:59");;
                }
            }

            $fechaInicioPeriodo = $fechaInicioPeriodo;
            $fechaFinPeriodo = $fechaFinPeriodo;

            /**
             * Comprobar si los parámetros inicio y fin no son nulos, si no lo son, entonces significa
             * que se requiere ver o actualizar una nómina antigua ya que estos valores solo son asignados
             * desde el componente de historial de pagos en Vue cuando se realiza una petición.
             */
            if ($inicio) {
                $fechaInicioPeriodo = $inicio;
            }

            if ($fin) {
                $fechaFinPeriodo = $fin;
            }
            $fechasNovedades = function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereBetween('date_start', [$fechaInicioPeriodo, $fechaFinPeriodo])->whereBetween('date_end', [$fechaInicioPeriodo, $fechaFinPeriodo])->with('disability_leave');
            };
            $funcionarios = Person::whereHas('contractultimate', function ($query) use ($fechaInicioPeriodo, $fechaFinPeriodo) {
                return $query->whereDate('date_of_admission', '<=', $fechaFinPeriodo)
                    ->whereDate('date_end', '>=', $fechaInicioPeriodo)->orWhereNull('date_end')
                    ->where('liquidated', '0');
            })->with(['payroll_factors' => $fechasNovedades])->get();

            foreach ($funcionarios as $funcionario) {
                $funcionariosResponse[] = $this->success([
                    'id' => $funcionario->id,
                    'identifier' => $funcionario->identifier,
                    'first_name' => $funcionario->first_name,
                    'first_surname' => $funcionario->first_surname,
                    'image' => $funcionario->image,
                    'salario_neto' => $this->getPagoNeto($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['total_valor_neto'],
                    'novedades' => $funcionario->novedades,
                    'horas_extras' => $this->getExtrasTotales($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['horas_reportadas'],
                    'novedades' => $this->getNovedades($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['novedades'],

                    'valor_ingresos_salariales' => ($this->getIngresos($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_constitutivos'] +
                        $this->getNovedades($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total'] +
                        $this->getExtrasTotales($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']),
                    'valor_ingresos_no_salariales' => $this->getIngresos($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_no_constitutivos'],
                    'valor_deducciones' => $this->getDeducciones($funcionario, $fechaInicioPeriodo, $fechaFinPeriodo)['valor_total']
                ]);
            }
            return $funcionariosResponse;
        } catch (\Throwable $th) {

            return [$th->getLine() . ' ' . $th->getFile() . ' ' . $th->getMessage()];
        }
    }

    /***
     * En estas funciones se declara que se recibirá el objeto Person
     * pero el nombre de la variable se usará $id, esto debido a que se está
     * usando para varias rutas la misma función que solo envían el id
     * pero Laravel al identificar que se requiere la persona, por medio del id
     * busca los demás datos
     */
    public function getPagoNeto(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaPago::pagoFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    /**
     * Calcular la cantidad y total de horas extras y recargos acumulados del funcionario en el periodo
     *
     * @param App\Models\Person $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getExtrasTotales(Person $id, $fechaInicio, $fechaFin)
    {
        return PayrollOvertime::extrasFuncionarioWithPerson($id)->fromTo($fechaInicio, $fechaFin);
    }

    /**
     * Calcular la cantidad y total de novedades del funcionario en el periodo
     *
     * @param App\Models\Person $id
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Illuminate\Support\Collection
     */
    public function getNovedades(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaNovedades::novedadesFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }


    public function getIngresos(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaIngresos::ingresosFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getDeducciones(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaDeducciones::deduccionesFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSeguridad(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaSeguridad::seguridadFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getRetenciones(Person $id, $fechaInicio, $fechaFin)
    {
        return  NominaRetenciones::retencionesFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getProvisiones(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaProvisiones::provisionesFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getSalario(Person $id, $fechaInicio, $fechaFin)
    {
        return NominaSalario::salarioFuncionarioWithPerson($id)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate();
    }

    public function getPorcentajes()
    {
        return response(PayrollSocialSecurityPerson::select('concept', 'percentage')->cursor(), 200);
    }
}
