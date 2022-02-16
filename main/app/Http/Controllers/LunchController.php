<?php

namespace App\Http\Controllers;

use App\Exports\LunchExport;
use App\Models\ExtraHourReport;
use App\Models\Lunch;
use App\Services\PersonService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LunchController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = DB::table('lunches as l')
        ->select(
            'l.id',
            'l.value',
            'p.first_name',
            'p.first_surname',
            'l.state',
            'l.created_at',
            'l.state',
            'l.person_id',
            'l.apply',
            DB::raw('concat(user.first_name," ",user.first_surname) as user')
        )
        ->when(request()->get('date_end'), function($q, $fill) 
        {
                $q->whereDate('l.created_at', '>=', request()->get('date_start'))
                ->whereDate('l.created_at', '<=', request()->get('date_end'));
        })
        ->when(request()->get('date_start'), function($q, $fill) 
        {
                $q->whereDate('l.created_at', '>=', request()->get('date_start'))
                ->whereDate('l.created_at', '<=', request()->get('date_end'));
        })
        ->when(request()->get('person'), function($q, $fill) 
        {
            $q->where('l.person_id', 'like', '%'.$fill.'%');
        })
        ->join('people as p', 'p.id', '=', 'l.person_id')
        ->join('users as u', 'u.id', '=', 'l.user_id')
        ->join('people as user', 'user.id', '=', 'u.person_id')
        ->orderByDesc('l.created_at')
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
        return $this->success($query);
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
            $data = $request->except('people');
            $lunches = $request->get('people');
            // if (count($lunches) > 0) {}
            if (!in_array('0', $data['people_id'])) {
                foreach ($lunches as $lunch) {
                    Lunch::create([
                        'user_id' => auth()->user()->id,
                        'value' => $lunch['value'],
                        'person_id' => $lunch['person_id'],
                        'dependency_id' => $data['dependency_id']
                    ]);
                }
            } else {
                $dataSe = [];
                if ($data['group_id'] != '0' && $data['dependency_id'] != '0' && in_array('0', $data['people_id'])) {
                    $dataSe = ['dependencies' => [$data['dependency_id']], 'groups' => [$data['group_id']]];
                }
                if ($data['group_id'] != '0' && $data['dependency_id'] == '0') {
                    $dataSe = ['dependencies' => [$data['dependency_id']]];
                }
                if ($data['group_id'] == '0') {
                    $dataSe = [];
                }
                $people =  PersonService::getPeople();
                foreach ($people as $person) {
                    Lunch::create([
                        'user_id' => auth()->user()->id,
                        'value' => $data['value'],
                        'person_id' => $person->id,
                        'dependency_id' => $data['dependency_id']
                    ]);
                }
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function activateOrInactivate( Request $request )
    {
        try {
            $state = Lunch::find($request->get('id'));
            $state->update($request->all());
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
        try {
            Lunch::find($id)->update($request->all());
            return $this->success('Valor editado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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

    public function download($fechaInicio, $fechaFin, Request $req)
    {
        $dates = [$fechaInicio,$fechaFin];
        return Excel::download(new LunchExport($dates),'reporte_almuerzos.xlsx');
    }

}
