<?php

namespace App\Http\Controllers;

use App\Http\Libs\Nomina\Facades\NominaCesantias;
use App\Models\Person;
use App\Models\SeveranceInterestPayment;
use App\Models\SeveranceInterestPaymentPerson;
use App\Models\SeverancePayment;
use App\Models\SeverancePaymentPerson;
use App\Models\Zones;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeverancePaymentController extends Controller
{
    use ApiResponser;
    public function paginate(Request $request)
    {
        $severancePayment = SeverancePayment::with('user')
            ->when($request->year, function ($q, $fill) {
                $q->where('year', 'like', "%$fill%");
            })
            ->when($request->type, function ($q, $fill) {
                $q->where('type', $fill);
            });
        $severanceInterestPayments = SeveranceInterestPayment::with('user')
            ->when($request->year, function ($q, $fill) {
                $q->where('year', 'like', "%$fill%");
            })
            ->when($request->type, function ($q, $fill) {
                $q->where('type', $fill);
            });
        $response = $severancePayment->union($severanceInterestPayments);
        return $this->success(
            $response->orderByDesc('year')
                ->paginate(
                    request()->get('pageSize', 10),
                    ['*'],
                    'page',
                    request()->get('page', 1)
                )
        );
    }

    public function validatPay(Request $request)
    {
        if ($request->type == 'pago') {
            $valid = SeverancePayment::where('year', $request->year)->exists();
        } else if ($request->type == 'pago-intereses') {
            $valid = SeveranceInterestPayment::where('year', $request->year)->exists();
        }
        return $this->success($valid);
    }

    public function getSeverancePayment()
    {
        try {
            $lastYear = Carbon::now()->subYear()->year;
            $inicio = Carbon::create($lastYear, 01, 01);
            $fin = Carbon::create($lastYear, 12, 30);
            $funcionarios = Person::with('severance_fund')
                ->with(['contractultimate' => function ($q) {
                    $q->select('id', 'person_id', 'salary', 'turn_type', 'date_end', 'work_contract_type_id', 'contract_term_id', 'position_id');
                }])
                ->with(['personPayrollPayments' => function ($query) use ($inicio, $fin) {
                    $query->whereBetween('created_at', [$inicio, $fin]);
                }])
                ->with(['provisionPersonPayrollPayments' => function ($query) use ($inicio, $fin) {
                    $query->whereBetween('created_at', [$inicio, $fin]);
                }])
                ->whereHas('contractultimate', function ($query) use ($fin) {
                    $query->where('work_contract_type_id', 1)
                        ->orWhere('work_contract_type_id', 2)
                        ->whereNull('date_end')
                        ->orWhere('date_end', '>=', $fin);
                })
                ->select('id', 'image', DB::raw("CONCAT_WS(' ', first_name, second_name, first_surname, second_surname) as full_names"))
                ->get();
            foreach ($funcionarios as $funcionario) {
                $funcionario['total_cesantias'] = ($this->getSeverancePerson($funcionario, $inicio, $fin));
            }
            return $this->success($funcionarios);
        } catch (\Throwable $th) {
            return $this->error(
                'msg: ' . $th->getMessage() . ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile(),
                204
            );
        }
    }

    public function getSeverancePerson($person, $inicio, $fin)
    {
        try {
            return NominaCesantias::cesantiaFuncionarioWithPerson($person)
                ->fromTo($inicio, $fin)
                ->calculate();
        } catch (\Throwable $th) {
            return $this->error('Msg: ' . $th->getMessage() . ' - Line: ' . $th->getLine() . ' - file: ' . $th->getFile(), 204);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            if ($request->type == 'pago') {
                $severancePayment = SeverancePayment::create([
                    'year' => $request->year,
                    'total' => $request->total_severance,
                    'total_employees' => count($request->people),
                    'user_id' => auth()->user()->id,
                    'type' => 'Pago a fondo de cesantías'
                ]);
                foreach ($request->people as $key => $person) {
                    SeverancePaymentPerson::create([
                        'severance_payment_id' => $severancePayment->id,
                        'person_id' => $person['id'],
                        'total' => $person['total_cesantias']['total_severance']
                    ]);
                }
            } else if ($request->type == 'pago-intereses') {
                $severanceInterestPayment = SeveranceInterestPayment::create([
                    'year' => $request->year,
                    'total' => $request->total_severance_interest,
                    'total_employees' => count($request->people),
                    'user_id' => auth()->user()->id,
                    'type' => 'Pago de intereses'
                ]);
                foreach ($request->people as $key => $person) {
                    SeveranceInterestPaymentPerson::create([
                        'severance_interest_payment_id' => $severanceInterestPayment->id,
                        'person_id' => $person['id'],
                        'total' => $person['total_cesantias']['total_severance_interest']
                    ]);
                }
            }
            return $this->success('Pagado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile(),204);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SeverancePayment  $severancePayment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(SeverancePayment::with('user', 'people')->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SeverancePayment  $severancePayment
     * @return \Illuminate\Http\Response
     */
    public function edit(SeverancePayment $severancePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeverancePayment  $severancePayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeverancePayment $severancePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SeverancePayment  $severancePayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeverancePayment $severancePayment)
    {
        //
    }
}

