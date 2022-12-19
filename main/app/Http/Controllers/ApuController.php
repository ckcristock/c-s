<?php

namespace App\Http\Controllers;

use App\Models\ApuPart;
use App\Models\ApuService;
use App\Models\ApuSet;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApuController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $query = DB::table('apu_parts as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('cities as c', 'ap.city_id', 'c.id')

            ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at', 'ap.unit_direct_cost as unit_cost')
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                     "apu_part" as type_module, "P" as type, "Parte" as type_name, c.name as city , false as selected '
            );

        $querySets = DB::table('apu_sets as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('cities as c', 'ap.city_id', 'c.id')
            /* ->join('users as u', 'ap.user_id', 'u.id') */
            /* ->join('people as p', 'u.person_id', 'p.id')
                      CONCAT_WS(" ",p.first_name,p.first_surname) as person_create
        */
            ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at', 'ap.total_direct_cost as unit_cost')
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                "apu_set" as type_module, "C" as type, "Conjunto" as type_name, c.name as city, false as selected '
            );
        $queryService = DB::table('apu_services as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('cities as c', 'ap.city_id', 'c.id')
            ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at', 'ap.general_subtotal_travel_expense_labor as unit_cost')
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                "apu_set" as type_module, "S" as type, "Servicio" as type_name, c.name as city, false as selected '
            )
            ->union($querySets)
            ->union($query);
        // $query->union($querySets);
        return $this->success(
            $queryService->get()
        );
    }


    public function paginate()
    {
        /* $query = ApuPart::with('thirdParty', 'city:id,name', 'person')
            ->extra()
            ->select('id as apu_id', 'name', 'code', 'observation', 'line');
        $querySets = ApuSet::with('thirdParty', 'city:id,name', 'person')
            ->extra()
            ->select('id as apu_id', 'name', 'code', 'observation', 'line')
            ->union($query)
            ->get();
        $queryService = ApuService::with('thirdParty', 'city:id,name', 'person')->select('line')->extra()->get();
        //$combinedQuery = $query->union($querySets)->union($queryService);
        return ($queryService); */
        $query = DB::table('apu_parts as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('municipalities as c', 'ap.city_id', 'c.id')
            ->join('people as p', 'p.id', 'ap.person_id')
            ->select(
                'ap.id as apu_id',
                'ap.name',
                'ap.code',
                'ap.observation',
                'line',
                'ap.created_at',
                'ap.unit_direct_cost as unit_cost',
                DB::raw('concat(p.first_name, " ", p.first_surname) as person')
            )->when(request()->get('code'), function ($q, $fill) {
                $q->where('ap.code', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('name'), function ($q, $fill) {
                $q->where('ap.name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('line'), function ($q, $fill) {
                $q->where('ap.line', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('description'), function ($q, $fill) {
                $q->where('ap.observation', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('city'), function ($q, $fill) {
                $q->where('c.name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('type'), function ($q, $fill) {
                $q->where('typeapu_name', $fill);
            })
            ->when(request()->get('client'), function ($q, $fill) {
                $q->where('tp.social_reason', 'like', '%' . $fill . '%');
            })
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                     "apu_part" as type_module, "P" as type,
                     "Pieza" as type_name, c.name as city , false as selected'
            )->get();

        $querySets = DB::table('apu_sets as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('municipalities as c', 'ap.city_id', 'c.id')
            ->join('people as p', 'p.id', 'ap.person_id')
            ->select(
                'ap.id as apu_id',
                'ap.name',
                'ap.code',
                'ap.observation',
                'line',
                'ap.created_at',
                'ap.total_direct_cost as unit_cost',
                DB::raw('concat(p.first_name, " ", p.first_surname) as person')
            )->when(request()->get('code'), function ($q, $fill) {
                $q->where('ap.code', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('name'), function ($q, $fill) {
                $q->where('ap.name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('line'), function ($q, $fill) {
                $q->where('ap.line', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('description'), function ($q, $fill) {
                $q->where('ap.observation', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('city'), function ($q, $fill) {
                $q->where('c.name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('type'), function ($q, $fill) {
                $q->where('typeapu_name', $fill);
            })
            ->when(request()->get('client'), function ($q, $fill) {
                $q->where('tp.social_reason', 'like', '%' . $fill . '%');
            })
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                "apu_set" as type_module, "C" as type,
                "Conjunto" as type_name, c.name as city, false as selected '
            );
        $queryService = DB::table('apu_services as ap')
            ->join('third_parties as tp', 'ap.third_party_id', 'tp.id')
            ->join('municipalities as c', 'ap.city_id', 'c.id')
            ->join('people as p', 'p.id', 'ap.person_id')
            ->select(
                'ap.id as apu_id',
                'ap.name',
                'ap.code',
                'ap.observation',
                'line',
                'ap.created_at',
                'ap.general_subtotal_travel_expense_labor as unit_cost',
                DB::raw('concat(p.first_name, " ", p.first_surname) as person')
            )->when(request()->get('code'), function ($q, $fill) {
                $q->where('ap.code', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('name'), function ($q, $fill) {
                $q->where('ap.name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('line'), function ($q, $fill) {
                $q->where('ap.line', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('description'), function ($q, $fill) {
                $q->where('ap.observation', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('city'), function ($q, $fill) {
                $q->where('c.name', 'like', '%' . $fill . '%');
            })
            ->selectRaw(
                'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
            "apu_set" as type_module, "S" as type,
                "Servicio" as type_name, c.name as city, false as selected '
            )
            ->when(request()->get('type'), function ($q, $fill) {
                $q->where('typeapu_name', $fill);
            })
            ->when(request()->get('client'), function ($q, $fill) {
                $q->where('tp.social_reason', 'like', '%' . $fill . '%');
            })
            ->union($querySets)
            ->union($query);


        // $query->union($querySets);
        return $this->success(
            $queryService

                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
        //
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
}
