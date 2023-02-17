<?php

namespace App\Http\Controllers;

use App\Http\Libs\Nomina\Facades\NominaCesantias;
use App\Models\Layoff;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LayoffController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            /**Empleados activos hasta el 31 de diciembre del año que terminó
             * salario y días trabajor
             */
            $lastYear = Carbon::now()->subYear()->year;

            $inicio = Carbon::create($lastYear,01,01);
            $fin = Carbon::create($lastYear,12,30);

            $funcionarios = Person::/* select('id', 'identifier', 'first_name', 'second_name', 'first_surname', 'second_surname', 'status')
            -> */with('personPayrollPayments', 'severance_fund')
            ->with(['contractultimate' => function($q) use ($inicio, $fin) {
                $q->whereNull('date_end')
                ->orWhere('date_end', '>=', $fin)
                ->select('id', 'person_id', 'salary', 'turn_type', 'date_end');
            }])
            ->get();
            foreach ($funcionarios as $funcionario) {
                //dd($funcionario);
                dd($this->getLayoffsPerson($funcionario, $inicio, $fin));
            }
            dd('asui');
            return $this->success($funcionarios);
        }catch (\Throwable $th){
            return $this->error(
            'msg: '. $th->getMessage(). ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile(), 204);
        }
    }

    public function paginate(Request $request)
    {

        try {
            $listado = Layoff::orderByDesc('period')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
            return $this->success($listado);
        }catch (\Throwable $th){
            return response()->json([
                'status' => 'error',
                'message' => 'message',
                'data' =>$th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile()
            ]);
        }
    }

    public function getCheckLayoffsList($anio)
    {
        try {
            $layoff = Layoff::where('period', $anio)->first();
            if ($layoff) {
                return $this->success($layoff);
            } else {
                return $this->error('Listado de cesantía no encontrado', 204);
            }
        }catch (\Throwable $th){
            return $this->error('Msg: ' .$th->getMessage(). ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile(), 204);
        }
    }

    public function getLayoffs (Request $request)
    {
        try {
            /**Empleados activos hasta el 31 de diciembre del año que terminó
             * salario y días trabajor
             */
            $data = Person::where('status', 'active')->get();
            return $this->success($data);
        }catch (\Throwable $th){
            return $this->error(
            'msg: '. $th->getMessage(). ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile(), 204);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->success('store');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function show(Layoff $layoff)
    {
        return $this->success('show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layoff $layoff)
    {
        return $this->success('update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layoff $layoff)
    {
        return $this->success('destroy');
    }

    /**
     * para calcular las cesantías
     */
    public function getLayoffsPerson($person, $fechaInicio, $fechaFin)
    {
        try {
            return $this->success(NominaCesantias::cesantiaFuncionarioWithPerson($person)
            ->fromTo($fechaInicio, $fechaFin)
            ->calculate());
        }catch (\Throwable $th){
        return $this->error('Msg: '.$th->getMessage().' - Line: '.$th->getLine().' - file: '.$th->getFile(), 204);
        }
    }
}
