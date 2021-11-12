<?php

namespace App\Services;

use App\Models\ApuPart;
use App\Models\ApuPartRawMaterial;
use App\Models\ApuPartRawMaterialMeasure;
use Exception;
use Illuminate\Support\Facades\Http;

class ApuPArtService
{
    static function saveApu($data)
	{
        $data["user_id"] = auth()->user()->id;
        $apuDB = ApuPart::create($data);
        $apuDB["code"] = $apuDB->id;
        $apuDB->save();
	    return $apuDB;
	}

    static function show($id){

        return ApuPart::with(["city",
                              "files",
                              "indirect",
                              "thirdparty" => function ($q) {
                                  $q->select('id', 'first_name', 'first_surname');
                              },
                              "machine" => function ($q) {
                                $q->select("*")
                                    ->with("unit");
                              },
                              "external"=> function ($q) {
                                $q->select("*")
                                    ->with("unit");
                              },
                              "internal"=> function ($q) {
                                $q->select("*")
                                    ->with("unit");
                              },
                              "other"=> function ($q) {
                                $q->select("*")
                                    ->with("unit");
                              },
                              "cutwater"=> function ($q) {
                                $q->select("*")
                                    ->with("material");
                              },
                              "cutlaser"=> function ($q) {
                                $q->select("*")
                                    ->with("material");
                              },
                              "commercial" => function ($q) {
                                $q->select("*")
                                    ->with('unit')
                                    ->with("material");
                              },
                            ])

                            ->with(["person" => function ($q) {
                                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                                },
                            ])
                            ->with([
                                "rawmaterial" => function ($q) {
                                    $q->select("*")
                                        ->with("geometry");
                                },
                                "rawmaterial.measures" => function ($q) {
                                    $q->select("*");
                                },
                                "rawmaterial.material" => function ($q) {
                                    $q->select("*");
                                },
                        ])
                    ->where("id", $id)
                    ->first();
    }

    static function deleteMaterial($id)
	{
        $mat =  ApuPartRawMaterial::where("apu_part_id", $id)->get();

        foreach ($mat as $value) {

            ApuPartRawMaterialMeasure::where("apu_part_raw_material_id",  $value["id"])->delete();
        }

        ApuPartRawMaterial::where("apu_part_id", $id)->delete();


	}

    static public function paginate(){

        return ApuPart::select(["id","third_party_id","user_id","person_id","city_id","name", "code", "line","amount","created_at", "state"])
                        ->with([
                            'user' => function ($q) {
                                $q->select("id","person_id");
                            },
                            'user.person' => function ($q) {
                                $q->select("id", "first_name", "first_surname");
                            },
                            'city' => function ($q) {
                                $q->select("id", "name");
                            },
                            'thirdparty' => function ($q) {
                                $q->select("id", "first_name", "first_surname");
                            },
                            'person' => function ($q) {
                                $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
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

                        // ->when( request()->get('state'), function($q, $fill)
                        // {
                        //     if (request()->get('state') == 'Todos') {
                        //         return null;
                        //     } else {
                        //         $q->where('state','like','%'.$fill.'%');
                        //     }
                        // })
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
    }
}
