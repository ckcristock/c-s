<?php

namespace App\Services;

use App\Models\ApuSet;

class ApuSetService
{
    static function show($id){

        return ApuSet::with(["city",
                              "files",
                              "thirdparty" => function ($q) {
                                  $q->select('id', 'first_name', 'first_surname');
                              },
                              "machine" => function ($q) {
                                $q->select("*");
                              },
                              "setpartlist"=> function ($q) {
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
                                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                                },
                        ])
                    ->where("id", $id)
                    ->first();
    }

    static public function paginate(){

        return ApuSet::select(["id","name","city_id","third_party_id","person_id","name", "observation", "unit_direct_cost", "line","created_at"])
                        ->with([
                            'city' => function ($q) {
                                $q->select("id", "name");
                            },
                            'thirdparty' => function ($q) {
                                $q->select("id", "first_name", "first_surname");
                            },
                            'person' => function ($q) {
                                $q->select("id", "first_name", "first_surname", "passport_number", "visa");
                            },
                            'internal' => function ($q) {
                                $q->select("id","apu_set_id", "description", "unit", "amount", "unit_cost", "total");
                            },
                            'external' => function ($q) {
                                $q->select("id","apu_set_id", "description", "unit", "amount", "unit_cost", "total");
                            },
                            'other' => function ($q) {
                                $q->select("id","apu_set_id", "description", "unit", "amount", "unit_cost", "total");
                            },
                            'indirect' => function ($q) {
                                $q->select("id","apu_set_id", "name", "percentage", "value");
                            }
                        ])
                        ->when( request()->get('name'), function($q, $fill)
                        {
                            $q->where('name','like','%'.$fill.'%');
                        })
                        ->when( request()->get('creation_date'), function($q, $fill)
                        {
                            $q->where('created_at', 'like','%'.$fill.'%');
                        })

        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
    }

}
