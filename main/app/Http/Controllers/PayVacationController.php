<?php

namespace App\Http\Controllers;

use App\Models\PayrollFactor;
use App\Models\PayVacation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class PayVacationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            DB::table('payroll_factors as pf')
            ->select(
                'pf.id',
                'pf.disability_leave_id',
                'pf.person_id',
                'pf.date_start',
                'pf.date_end',
                DB::raw('concat(p.first_name," ",p.first_surname) as name'),
                'p.image',
                'w.salary',
                'depe.name as dependency',
                // 'pv.state'
            )
            // ->join('pay_vacations as pv', 'pv.payroll_factor_id', '=', 'pf.id')
            ->join('people as p', 'p.id', '=', 'pf.person_id')
            ->join('disability_leaves as d', 'd.id', '=', 'pf.disability_leave_id')
            ->join('work_contracts as w', 'w.person_id', '=', 'p.id')
            ->join('positions as posi', 'posi.id', '=', 'w.position_id')
            ->join('dependencies as depe', 'depe.id', '=', 'posi.dependency_id')
            ->where('d.concept', '=', 'Vacaciones')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function paginate () 
    {
        return $this->success(
            PayrollFactor::with('person', 'pay_vacations')
            ->whereHas('disability_leave', function($query) {
                return $query->where('concept', 'Vacaciones');
            })
            ->orderByDesc('updated_at')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1)));
    }


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
        return $this->success(
            PayVacation::create($request->all())
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function download($id){
        $vacation = PayrollFactor::with('person', 'pay_vacations')
        ->where('id', $id)
        ->whereHas('disability_leave', function($query) {
            return $query->where('concept', 'Vacaciones');
        })
        ->first();
        $contenido =
            '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">  
                <style>
                    table, th, td {
                        border: 1px solid;
                        padding: 10px;
                    }
                    table {
                        border-collapse: collapse;
                      }
                </style>             
            </head>
            <body style="margin:2rem">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha de inicio</th>
                            <th>Fecha de fin</th>
                            <th>N° días</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>' . $vacation->person->full_name . '</td>
                        <td>' . Carbon::parse($vacation->date_start)->locale('es')->isoFormat('DD MMMM YYYY') . '</td>
                        <td>' . Carbon::parse($vacation->date_end)->locale('es')->isoFormat('DD MMMM YYYY') . '</td>
                        <td>' . $vacation->pay_vacations[0]->days . '</td>
                        <td>$' . number_format( $vacation->pay_vacations[0]->value, 0, "", ".") . '</td>
                    </tr>
                    </tbody>
                </table>
            </body>
        </html>';
        $pdf = PDF::loadHTML($contenido);
        return $pdf->download('certificado.pdf');
    }
}
