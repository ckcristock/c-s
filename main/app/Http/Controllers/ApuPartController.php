<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApuPartService;
use App\Services\RawMaterialService;
use App\Traits\ApiResponser;
use App\Models\ApuPartCommercialMaterial;
use App\Models\ApuPartCutLaser;
use App\Models\ApuPartCutWater;
use App\Models\ApuPartExternalProcess;
use App\Models\ApuPartFile;
use App\Models\ApuPartIndirectCost;
use App\Models\ApuPartInternalProcess;
use App\Models\ApuPartMachineTool;
use App\Models\ApuPartOther;

class ApuPartController extends Controller
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
        $data = $request->except([
			"files",
			"materia_prima",
			"commercial_materials",
			"cut_water",
			"cut_laser",
			"machine_tools",
			"internal_proccesses",
			"external_proccesses",
			"others",
			"indirect_cost",
		]);

        $files = request()->get("files");
        $materia_prima = request()->get("materia_prima");
        $commercial_materials = request()->get("commercial_materials");
        $cut_water = request()->get("cut_water");
        $cut_laser = request()->get("cut_laser");
        $machine_tools = request()->get("machine_tools");
        $internal_proccesses = request()->get("internal_proccesses");
        $external_proccesses = request()->get("external_proccesses");
        $others = request()->get("others");
        $indirect_cost = request()->get("indirect_cost");

        try {

            $apu = ApuPartService::saveApu($data);
            $id = $apu->id;

            foreach ($files as $file){
				$file["apu_part_id"] = $id;
				ApuPartFile::create($file);
			}

            RawMaterialService::SaveRawMaterial($materia_prima,$apu);

            foreach ($commercial_materials as $cmaterials){
				$cmaterials["apu_part_id"] = $id;
				ApuPartCommercialMaterial::create($cmaterials);
			}

            foreach ($cut_water as $cwater){
				$cwater["apu_part_id"] = $id;
				ApuPartCutWater::create($cwater);
			}

            foreach ($cut_laser as $claser){
				$claser["apu_part_id"] = $id;
				ApuPartCutLaser::create($claser);
			}

            foreach ($machine_tools as $mtool){
				$mtool["apu_part_id"] = $id;
				ApuPartMachineTool::create($mtool);
			}

            foreach ($internal_proccesses as $iproccesses){
				$iproccesses["apu_part_id"] = $id;
				ApuPartInternalProcess::create($iproccesses);
			}

            foreach ($external_proccesses as $eproccesses){
				$eproccesses["apu_part_id"] = $id;
				ApuPartExternalProcess::create($eproccesses);
			}

            foreach ($others as $other){
				$other["apu_part_id"] = $id;
				ApuPartOther::create($other);
			}

            foreach ($indirect_cost as $icost){
				$icost["apu_part_id"] = $id;
				ApuPartIndirectCost::create($icost);
			}


            return $this->success("guardado con éxito");

        }catch (\Throwable $th) {

            return $this->errorResponse($th->getMessage(), 500);
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
        return $this->success(
			ApuPartService::show($id)
		);
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
