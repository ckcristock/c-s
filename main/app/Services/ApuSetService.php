<?php

namespace App\Services;

use App\Models\ApuSet;
use Illuminate\Support\Facades\DB;

class ApuSetService
{
    static function show($id){

        return ApuSet::with(["city",
                              "files",
                              "thirdparty" => function ($q) {
                                $q->select('id', DB::raw('concat(first_name, " ", first_surname) as name'));
                              },
                              "machine" => function ($q) {
                                $q->select("*");
                              },
                              "setpartlist"=> function ($q) {
                                $q->select("*");
                              },
                              "setpartlist.apuset"=> function ($q) {
                                $q->select("*");
                              },
                              "setpartlist.apupart"=> function ($q) {
                                $q->select("*");
                              },
                              "internal"=> function ($q) {
                                $q->select("*");
                              },
                              "external"=> function ($q) {
                                $q->select("*");
                              },
                              "other"=> function ($q) {
                                $q->select("*");
                              },
                              "indirect"=> function ($q) {
                                $q->select("*");
                              },
                            ])
                            ->with(["person" => function ($q) {
                                $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'), 'passport_number', 'visa');
                            },
                            ])
                            ->where("id", $id)
                            ->first();
    }
    static function find($name){

        return ApuSet::with(["city",
                              "files",
                              "thirdparty" => function ($q) {
                                $q->select('id', DB::raw('concat(first_name, " ", first_surname) as name'));
                              },
                              "machine" => function ($q) {
                                $q->select("*");
                              },
                              "setpartlist"=> function ($q) {
                                $q->select("*");
                              },
                              "setpartlist.apuset"=> function ($q) {
                                $q->select("*");
                              },
                              "setpartlist.apupart"=> function ($q) {
                                $q->select("*");
                              },
                              "internal"=> function ($q) {
                                $q->select("*");
                              },
                              "external"=> function ($q) {
                                $q->select("*");
                              },
                              "other"=> function ($q) {
                                $q->select("*");
                              },
                              "indirect"=> function ($q) {
                                $q->select("*");
                              },
                            ])
                            ->with(["person" => function ($q) {
                                $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'), 'passport_number', 'visa');
                            },
                            ])
                            ->when($name, function($q,$fill)
                            {
                              $q->where(  'name', 'like', "%$fill%" );
                            })
                            ->get(['*','id as value', 'name as text']);
    }

    static public function paginate(){

        return ApuSet::select(["id","name","city_id","third_party_id","person_id","name", "observation", "line","created_at", "state", "code"])
                        ->with([
                            'city' => function ($q) {
                                $q->select("id", "name");
                            },
                            'thirdparty' => function ($q) {
                                $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'));
                            },
                            'person' => function ($q) {
                                $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'), "passport_number", "visa");
                            }
                        ])
                        ->when( request()->get('name'), function($q, $fill)
                        {
                            $q->where('name','like','%'.$fill.'%');
                            dd($fill);
                        })
                        ->when( request()->get('creation_date'), function($q, $fill)
                        {
                            $q->where('created_at', 'like','%'.$fill.'%');
                        })

        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
    }

}
