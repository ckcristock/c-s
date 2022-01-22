<?php

namespace App\Http\Controllers;

use App\Models\DisabilityLeave;
use App\Models\PayrollFactor;
use App\Models\Person;
use App\Traits\ApiResponser;
use App\Traits\PayrollFactorDates;
use Illuminate\Http\Request;

class PayrollFactorController extends Controller
{
    //
    use PayrollFactorDates;
    use ApiResponser;

    public function store(Request $request)
    {
        if ( $request->get('id') || $this->customValidate($request->all()) ) {

            $values = $request->get('id') ? $request->all() : $this->pushFlag($request->all()) ;
            PayrollFactor::updateOrCreate(['id' => $request->get('id')], $values);
            return $this->success('Novedad creada correctamente');
        }
        return $this->error('El funcionario ya se encuentra con novedades registradas en este periodo', 422);
    }

    public function indexByPeople()
    {
        return $this->success(Person::with(
            [
                'payroll_factors' => function ($q) {
                    $q->when(request()->get('personfill'), function($q, $fill) 
                    {
                        $q->where('person_id', 'like', '%'.$fill.'%');
                    });
                    $q->when(request()->get('date_end'), function($q, $fill) 
                    {
                        $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                    });
                    $q->when(request()->get('date_start'), function($q, $fill) 
                    {
                        $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                    });
                    // $q->where('person_id', '=', request()->get('personfill'));
                    // $q->where('date_start', '>=', request()->get('date_start'))
                    //     ->where('date_end', '<=', request()->get('date_end'));
                },
                'payroll_factors.disability_leave' => function ($q) {
                },
                'contractultimate' => function ($q) {
                }
            ]
        )
        ->whereHas('payroll_factors', function ($q) {
            $q->when(request()->get('personfill'), function($q, $fill) 
            {
                $q->where('person_id', 'like', '%'.$fill.'%');
            });
            $q->when(request()->get('date_end'), function($q, $fill) 
                    {
                        $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                    });
                    $q->when(request()->get('date_start'), function($q, $fill) 
                    {
                        $q->where('date_start', '>=', request()->get('date_start'))
                        ->where('date_end', '<=', request()->get('date_end'));
                    });
        // $q->where('person_id', '=', request()->get('personfill'));
                // $q->where('date_start', '>=', request()->get('date_start'))
                //     ->where('date_end', '<=', request()->get('date_end'));
        })
        ->get());
    }


    public function pushFlag(array $request): array
    {
        $request['sum'] = (DisabilityLeave::find($request['disability_leave_id'], ['sum']))->sum;
        return  $request;
    }
}
