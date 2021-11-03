<?php

namespace App\Services;

use App\Models\ApuPartRawMaterial;
use App\Models\ApuPartRawMaterialMeasure;

class RawMaterialService
{
    static function SaveRawMaterial($materia_prima,$apu)
	{
       foreach ($materia_prima as $mprima) {
        $mprima["apu_part_id"] = $apu->id;
        $rmaterial = ApuPartRawMaterial::create($mprima);
        
        foreach ($mprima["measures"] as $value) {
            $value["apu_part_raw_material_id"] =  $rmaterial["id"];
            ApuPartRawMaterialMeasure::create($value);
        }
       }

	}
}
