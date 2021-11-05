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
	   return ApuPart::create($data);
	}

    static function show($id){

        return ApuPart::with(["city",
                              "files",
                              "thirdparty",
                              "machine",
                              "external",
                              "internal",
                              "other",
                              "indirect",
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
                                    ->with("material");
                              },
                            ])

                            ->with(["person" => function ($q) {
                                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                                },
                            ])
                            ->with([
                                "rowmaterial" => function ($q) {
                                    $q->select("*")
                                        ->with("geometry");
                                },
                                "rowmaterial.measures" => function ($q) {
                                    $q->select("*");
                                },
                                "rowmaterial.material" => function ($q) {
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

	}
}
