<?php

namespace App\Http\Controllers;

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
            
             ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at' ,'ap.unit_direct_cost as unit_cost')
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
        ->select('ap.id as apu_id', 'ap.name', 'line', 'ap.created_at' , 'ap.total_direct_cost as unit_cost')
        ->selectRaw(
            'IFNULL(tp.social_reason, CONCAT_WS(" ",tp.first_name,tp.first_name) ) as custumer,
                "apu_set" as type_module, "P" as type, "Conjunto" as type_name, c.name as city, false as selected '
        );
        $query->union($querySets);
        return $this->success(
            $query->get()
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
