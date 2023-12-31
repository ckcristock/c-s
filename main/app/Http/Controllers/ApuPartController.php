<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApuPartService;
use App\Services\RawMaterialService;
use App\Traits\ApiResponser;
use App\Models\ApuPart;
use App\Models\ApuPartCommercialMaterial;
use App\Models\ApuPartCutLaser;
use App\Models\ApuPartCutWater;
use App\Models\ApuPartExternalProcess;
use App\Models\ApuPartFile;
use App\Models\ApuPartIndirectCost;
use App\Models\ApuPartInternalProcess;
use App\Models\ApuPartMachineTool;
use App\Models\ApuPartOther;
use App\Models\ApuPartRawMaterial;
use App\Models\ApuPartRawMaterialMeasure;
use App\Models\Company;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

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
        return $this->success(
            ApuPartService::paginate()
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
            foreach ($files as $file) {
                if ($file['type'] == 'image/jpeg' || $file['type'] == 'image/jpg' || $file['type'] == 'image/png') {
                    $type = '.' . explode('/', $file['type'])[0];
                    $base64 = saveBase64($file['base64'], 'apu-parts/', true, $type);
                    $file_api = URL::to('/') . '/api/image?path=' . $base64;
                } else {
                    $base64 = saveBase64File($file['base64'], 'apu-parts/', false, '.pdf');
                    $file_api = URL::to('/') . '/api/file-view?path=' . $base64;
                }
                ApuPartFile::create([
                    'apu_part_id' => $id,
                    'file' => $file_api,
                    'name' => $file['name'],
                    'type' => $file['type']
                ]);
            }

            RawMaterialService::SaveRawMaterial($materia_prima, $apu);

            foreach ($commercial_materials as $cmaterials) {
                $cmaterials["apu_part_id"] = $id;
                ApuPartCommercialMaterial::create($cmaterials);
            }

            foreach ($cut_water as $cwater) {
                $cwater["apu_part_id"] = $id;
                ApuPartCutWater::create($cwater);
            }

            foreach ($cut_laser as $claser) {
                $claser["apu_part_id"] = $id;
                ApuPartCutLaser::create($claser);
            }

            foreach ($machine_tools as $mtool) {
                $mtool["apu_part_id"] = $id;
                ApuPartMachineTool::create($mtool);
            }

            foreach ($internal_proccesses as $iproccesses) {
                $iproccesses["apu_part_id"] = $id;
                ApuPartInternalProcess::create($iproccesses);
            }

            foreach ($external_proccesses as $eproccesses) {
                $eproccesses["apu_part_id"] = $id;
                ApuPartExternalProcess::create($eproccesses);
            }

            foreach ($others as $other) {
                $other["apu_part_id"] = $id;
                ApuPartOther::create($other);
            }

            foreach ($indirect_cost as $icost) {
                $icost["apu_part_id"] = $id;
                ApuPartIndirectCost::create($icost);
            }

            sumConsecutive('apu_parts');
            return $this->success($apu);
        } catch (\Throwable $th) {

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
    public function find(Request $req)
    {
        return $this->success(
            ApuPartService::find($req->get('name'))
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

        try {
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
            $apu = ApuPart::find($id);
            $apu->update($data);
            if ($files) {
                foreach ($files as $file) {
                    if ($file['type'] == 'image/jpeg' || $file['type'] == 'image/jpg' || $file['type'] == 'image/png') {
                        $type = '.' . explode('/', $file['type'])[0];
                        $base64 = saveBase64($file['base64'], 'apu-parts/', true, $type);
                        $file_api = URL::to('/') . '/api/image?path=' . $base64;
                    } else {
                        $base64 = saveBase64File($file['base64'], 'apu-parts/', false, '.pdf');
                        $file_api = URL::to('/') . '/api/file-view?path=' . $base64;
                    }
                    ApuPartFile::create([
                        'apu_part_id' => $id,
                        'file' => $file_api,
                        'name' => $file['name'],
                        'type' => $file['type']
                    ]);
                }
            }
            ApuPartService::deleteMaterial($id);
            if ($materia_prima) {
                //  echo json_encode($materia_prima);
                foreach ($materia_prima as $mprima) {
                    $mprima["apu_part_id"] = $id;
                    $rmaterial = ApuPartRawMaterial::create($mprima);
                    foreach ($mprima["measures"] as $value) {
                        $value["apu_part_raw_material_id"] =  $rmaterial["id"];
                        // echo json_encode($value);
                        ApuPartRawMaterialMeasure::create($value);
                    }
                }
            }
            // foreach ($materia_prima as $mprima) {
            //     $mprima["apu_part_id"] = $apu->id;
            //     $rmaterial = ApuPartRawMaterial::create($mprima);

            //     foreach ($mprima["measures"] as $value) {
            //         $value["apu_part_raw_material_id"] =  $rmaterial["id"];
            //         ApuPartRawMaterialMeasure::create($value);
            //     }
            //    }
            ApuPartCommercialMaterial::where("apu_part_id", $id)->delete();
            if ($commercial_materials) {
                foreach ($commercial_materials as $cmaterials) {
                    $cmaterials["apu_part_id"] = $id;
                    ApuPartCommercialMaterial::create($cmaterials);
                }
            }
            ApuPartCutWater::where("apu_part_id", $id)->delete();
            if ($cut_water) {
                foreach ($cut_water as $cwater) {
                    $cwater["apu_part_id"] = $id;
                    ApuPartCutWater::create($cwater);
                }
            }
            ApuPartCutLaser::where("apu_part_id", $id)->delete();
            if ($cut_laser) {
                foreach ($cut_laser as $claser) {
                    $claser["apu_part_id"] = $id;
                    ApuPartCutLaser::create($claser);
                }
            }
            ApuPartMachineTool::where("apu_part_id", $id)->delete();
            if ($machine_tools) {
                foreach ($machine_tools as $mtool) {
                    $mtool["apu_part_id"] = $id;
                    ApuPartMachineTool::create($mtool);
                }
            }
            ApuPartInternalProcess::where("apu_part_id", $id)->delete();
            if ($internal_proccesses) {
                foreach ($internal_proccesses as $iproccesses) {
                    $iproccesses["apu_part_id"] = $id;
                    ApuPartInternalProcess::create($iproccesses);
                }
            }
            ApuPartExternalProcess::where("apu_part_id", $id)->delete();
            if ($external_proccesses) {
                foreach ($external_proccesses as $eproccesses) {
                    $eproccesses["apu_part_id"] = $id;
                    ApuPartExternalProcess::create($eproccesses);
                }
            }
            ApuPartOther::where("apu_part_id", $id)->delete();
            if ($others) {
                foreach ($others as $other) {
                    $other["apu_part_id"] = $id;
                    ApuPartOther::create($other);
                }
            }
            ApuPartIndirectCost::where("apu_part_id", $id)->delete();
            if ($indirect_cost) {
                foreach ($indirect_cost as $icost) {
                    $icost["apu_part_id"] = $id;
                    ApuPartIndirectCost::create($icost);
                }
            }
            return $this->success($apu);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage() . ' ' . $th->getLine(), $th->getFile(), 500);
        }
    }

    public function activateOrInactivate(Request $request)
    {
        try {
            $state = ApuPart::find($request->get('id'));
            $state->update($request->all());
            return $this->success('Actualizado con éxito');
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

    public function deleteFile($id)
    {
        ApuPartFile::find($id)->delete();
        return $this->success('Plano eliminado con éxtio');
    }

    public function pdf($id)
    {
        $company = Company::first();
        $image = $company->page_heading;
        $data = ApuPartService::show($id);
        //return $data;
        $datosCabecera = (object) array(
            'Titulo' => 'APU Pieza',
            'Codigo' => $data->code,
            'Fecha' => $data->created_at,
            'CodigoFormato' => $data->format_code
        );
        $pdf = PDF::loadView('pdf.apu_pieza', [
            'data' => $data,
            'company' => $company,
            'datosCabecera' => $datosCabecera,
            'image' => $image
        ]);
        return $pdf->setPaper('A4', 'landscape')->download('apu_pieza.pdf');
    }
}
