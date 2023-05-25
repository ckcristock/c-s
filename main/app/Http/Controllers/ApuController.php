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
            ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at', 'ap.total_unit_cost as unit_cost')
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


    public function paginate(Request $request)
    {
        $query = ApuPart::with(
            'thirdParty',
            'city:id,name',
            'person:id,first_name,first_surname,full_name',
        )
            ->extra($request)
            ->when($request->city, function ($q, $fill) {
                $q->whereHas('city', function ($query) use ($fill) {
                    $query->where('name', 'like', "%$fill%");
                });
            })
            ->when($request->client, function ($q, $fill) {
                $q->whereHas('thirdParty', function ($query) use ($fill) {
                    $query->where('social_reason', 'like', "%$fill%");
                });
            });

        $querySets = ApuSet::with([
            'thirdParty',
            'city:id,name',
            'person:id,first_name,first_surname,full_name',
        ])
            ->extra($request)
            ->when($request->city, function ($q, $fill) {
                $q->whereHas('city', function ($query) use ($fill) {
                    $query->where('name', 'like', "%$fill%");
                });
            })
            ->when($request->client, function ($q, $fill) {
                $q->whereHas('thirdParty', function ($query) use ($fill) {
                    $query->where('social_reason', 'like', "%$fill%");
                });
            });
        $queryService = ApuService::with('thirdParty', 'city:id,name', 'person:id,first_name,first_surname,full_name')
            ->extra($request)
            ->when($request->city, function ($q, $fill) {
                $q->whereHas('city', function ($query) use ($fill) {
                    $query->where('name', 'like', "%$fill%");
                });
            })
            ->when($request->client, function ($q, $fill) {
                $q->whereHas('thirdParty', function ($query) use ($fill) {
                    $query->where('social_reason', 'like', "%$fill%");
                });
            });
        if ($request->type_multiple == 'pieza') {
            $query_total = $query;
        } else if ($request->type_multiple == 'conjunto') {
            $query_total = $querySets;
        } else if ($request->type_multiple == 'servicio') {
            $query_total = $queryService;
        } else if ($request->type_multiple == 'pyc') {
            $query_total = $query->union($querySets);
        } else {
            $query_total = $queryService
                ->union($query)
                ->union($querySets);
        }

        return $this->success(
            $query_total->orderByDesc('created_at')->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function getApuPartToAddInSet(Request $request, $id)
    {
        $query = ApuPart::with(
            'thirdParty',
            'city:id,name',
            'person:id,first_name,first_surname,full_name',
        )
            ->extra($request)
            ->where('id', $id)
            ->get();
        return $this->success($query);
    }

    public function getApuSetToAdd(Request $request, $id)
    {
        $query = ApuSet::with([
            'thirdParty',
            'city:id,name',
            'person:id,first_name,first_surname,full_name',
        ])
            ->extra($request)
            ->where('id', $id)
            ->get();
        return $this->success($query);
    }

    public function getApuServiceToAdd(Request $request, $id)
    {
        $query = ApuService::with('thirdParty', 'city:id,name', 'person:id,first_name,first_surname,full_name')
            ->extra($request)
            ->where('id', $id)
            ->get();
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
