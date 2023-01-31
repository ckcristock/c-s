<?php

namespace App\Http\Controllers;

use App\Models\ApuService;
use App\Models\ApuServiceAssemblyStartUp;
use App\Models\ApuServiceDimensionalValidation;
use App\Models\ApuServiceTravelEstimationAssemblyStartUp;
use App\Models\ApuServiceTravelEstimationDimensionalValidation;
use App\Models\Company;
use App\Models\Municipality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade as PDF;

class ApuServiceController extends Controller
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

    public function paginate()
    {
        return $this->success(
            ApuService::select([
                "id", "third_party_id", "person_id", "city_id", "name", "code", "line", "created_at", "state"
            ])
                ->with([
                    'person' => function ($q) {
                        $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'));
                    },
                    'city' => function ($q) {
                        $q->select("id", "name");
                    },
                    'thirdparty' => function ($q) {
                        $q->select("id", DB::raw('concat(first_name, " ", first_surname) as name'));
                    }
                ])
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->when(request()->get('creation_date'), function ($q, $fill) {
                    $q->where('created_at', 'like', '%' . $fill . '%');
                })
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
        $data = $request->except([
            "calculate_labor",
            "mpm_calculate_labor"
        ]);

        $calculate_labor = request()->get("calculate_labor");
        $mpm_calculate_labor = request()->get("mpm_calculate_labor");

        try {
            $consecutive = getConsecutive('apu_services');
            if ($consecutive->city) {
                $abbreviation = Municipality::where('id', $data['city_id'])->first()->abbreviation;
                $data['code'] = generateConsecutive('apu_services', $abbreviation);
            } else {
                $data['code'] = generateConsecutive('apu_services');
            }
            $apuservice = ApuService::create($data);
            $id = $apuservice->id;
            $apuservice->save();

            foreach ($calculate_labor as $cl) {
                $cl["apu_service_id"] = $id;
                $dimsionalValid = ApuServiceDimensionalValidation::create($cl);
                foreach ($cl["viatic_estimation"] as $value) {
                    $value["apu_service_dimensional_validation_id"] = $dimsionalValid["id"];
                    ApuServiceTravelEstimationDimensionalValidation::create($value);
                }
            }

            foreach ($mpm_calculate_labor as $mpm_cl) {
                $mpm_cl["apu_service_id"] = $id;
                $assemblyStart = ApuServiceAssemblyStartUp::create($mpm_cl);
                foreach ($mpm_cl["viatic_estimation"] as $value) {
                    $value["apu_service_assembly_start_up_id"] = $assemblyStart["id"];
                    ApuServiceTravelEstimationAssemblyStartUp::create($value);
                }
            }
            sumConsecutive('apu_services');
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
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
            ApuService::with([
                "city",
                "person" => function ($q) {
                    $q->select('id', DB::raw('concat(first_name, " ", first_surname) as name'));
                },
                "thirdparty" => function ($q) {
                    $q->select('id', DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as name'));
                },
                "dimensionalValidation" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "dimensionalValidation.travelEstimationDimensionalValidations" => function ($q) {
                    $q->select("*");
                },
                "assembliesStartUp" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "assembliesStartUp.travelEstimationAssembliesStartUp" => function ($q) {
                    $q->select("*");
                }
            ])
                ->where("id", $id)
                ->first()
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
                "calculate_labor",
                "mpm_calculate_labor"
            ]);

            $calculate_labor = request()->get("calculate_labor");
            $mpm_calculate_labor = request()->get("mpm_calculate_labor");

            ApuService::find($id)->update($data);

            if ($calculate_labor) {
                $data =  ApuServiceDimensionalValidation::where("apu_service_id", $id)->get();
                foreach ($data as $value) {
                    ApuServiceTravelEstimationDimensionalValidation::where("apu_service_dimensional_validation_id",  $value["id"])->delete();
                }
                ApuServiceDimensionalValidation::where("apu_service_id", $id)->delete();
                foreach ($calculate_labor as $cl) {
                    $cl["apu_service_id"] = $id;
                    $dimsionalValid = ApuServiceDimensionalValidation::create($cl);
                    foreach ($cl["viatic_estimation"] as $value) {
                        $value["apu_service_dimensional_validation_id"] = $dimsionalValid["id"];
                        ApuServiceTravelEstimationDimensionalValidation::create($value);
                    }
                }
            }

            if ($mpm_calculate_labor) {
                $data =  ApuServiceAssemblyStartUp::where("apu_service_id", $id)->get();
                foreach ($data as $value) {
                    ApuServiceTravelEstimationAssemblyStartUp::where("apu_service_assembly_start_up_id",  $value["id"])->delete();
                }
                ApuServiceAssemblyStartUp::where("apu_service_id", $id)->delete();
                foreach ($mpm_calculate_labor as $mpm_cl) {
                    $mpm_cl["apu_service_id"] = $id;
                    $assemblyStart = ApuServiceAssemblyStartUp::create($mpm_cl);
                    foreach ($mpm_cl["viatic_estimation"] as $value) {
                        $value["apu_service_assembly_start_up_id"] = $assemblyStart["id"];
                        ApuServiceTravelEstimationAssemblyStartUp::create($value);
                    }
                }
            }

            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function activateOrInactivate(Request $request)
    {
        try {
            $state = ApuService::find($request->get('id'));
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

    public function pdf($id)
    {
        $company = Company::first();
        //$data = ApuService::find($id);
        $datosCabecera = (object) array(
            'Titulo' => 'APU Servicio',
            'Codigo' => 'APS-001-23',
            'Fecha' => '22-03-03',
            'CodigoFormato' => 'APU VERSION 01'
        );
        $image = $company->page_heading;
        //return $company;
        //return $datosCabecera;
		$pdf = PDF::loadView('components.cabecera', [
            'company' => $company,
            'datosCabecera' => $datosCabecera,
            'image' => $image
        ]);
		return $pdf->download('apu_set.pdf');
        return View::make('components/cabecera')->with(compact('company', 'datosCabecera', 'image'));
    }

}
