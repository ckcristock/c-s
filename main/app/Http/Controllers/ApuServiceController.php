<?php

namespace App\Http\Controllers;

use App\Models\ApuService;
use App\Models\ApuServiceAccompaniment;
use App\Models\ApuServiceAccompanimentContractor;
use App\Models\ApuServiceAssemblyEquipmentContractor;
use App\Models\ApuServiceAssemblyStartUp;
use App\Models\ApuServiceDimensionalValidation;
use App\Models\ApuServiceDimensionalValidationContractors;
use App\Models\ApuServiceTravelEstimationAccompaniment;
use App\Models\ApuServiceTravelEstimationAccompanimentContractor;
use App\Models\ApuServiceTravelEstimationAssemblyEquipmentContractor;
use App\Models\ApuServiceTravelEstimationAssemblyStartUp;
use App\Models\ApuServiceTravelEstimationDimensionalValidation;
use App\Models\ApuServiceTravelEstimationDimensionalValidationContractors;
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
            "mpm_calculate_labor",
            "apm_calculate_labor",
            "c_apm_calculate_labor",
            "c_me_calculate_labor",
            "c_vd_calculate_labor",
        ]);

        $calculate_labor = request()->get("calculate_labor");
        $mpm_calculate_labor = request()->get("mpm_calculate_labor");
        $apm_calculate_labor = request()->get("apm_calculate_labor");
        $c_apm_calculate_labor = request()->get("c_apm_calculate_labor");
        $c_me_calculate_labor = request()->get("c_me_calculate_labor");
        $c_vd_calculate_labor = request()->get("c_vd_calculate_labor");

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

            foreach ($apm_calculate_labor as $apm_cl) {
                $apm_cl["apu_service_id"] = $id;
                $accompaniment = ApuServiceAccompaniment::create($apm_cl);
                foreach ($apm_cl["viatic_estimation"] as $value) {
                    $value["apu_service_accompaniments_id"] = $accompaniment["id"];
                    ApuServiceTravelEstimationAccompaniment::create($value);
                }
            }
            //!Contratistas c_vd_calculate_labor
            foreach ($c_apm_calculate_labor as $c_amp_cl) {
                $c_amp_cl["apu_service_id"] = $id;
                $accompanimentC = ApuServiceAccompanimentContractor::create($c_amp_cl);
                foreach ($c_amp_cl["viatic_estimation"] as $value) {
                    $value["apu_service_accompaniment_contractors_id"] = $accompanimentC["id"];
                    ApuServiceTravelEstimationAccompanimentContractor::create($value);
                }
            }

            foreach ($c_me_calculate_labor as $c_me_cl) {
                $c_me_cl["apu_service_id"] = $id;
                $assemblyEquipentC = ApuServiceAssemblyEquipmentContractor::create($c_me_cl);
                foreach ($c_me_cl["viatic_estimation"] as $value) {
                    $value["apu_service_assembly_equipment_contractor_id"] = $assemblyEquipentC["id"];
                    ApuServiceTravelEstimationAssemblyEquipmentContractor::create($value);
                }
            }

            foreach ($c_vd_calculate_labor as $c_vd_cl) {
                $c_vd_cl["apu_service_id"] = $id;
                $dimsionalValidC = ApuServiceDimensionalValidationContractors::create($c_vd_cl);
                foreach ($c_vd_cl["viatic_estimation"] as $value) {
                    $value["apu_service_dimensional_validation_contractors_id"] = $dimsionalValidC["id"];
                    ApuServiceTravelEstimationDimensionalValidationContractors::create($value);
                }
            }

            sumConsecutive('apu_services');
            return $this->success($apuservice);
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
                },
                "accompaniments" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "accompaniments.travelEstimationAccompaniment" => function ($q) {
                    $q->select("*");
                },
                //!contratistas
                "dimensionalValidationC" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "dimensionalValidationC.travelEstimationDimensionalValidationsC" => function ($q) {
                    $q->select("*");
                },
                "assembliesStartUpC" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "assembliesStartUpC.travelEstimationAssembliesStartUpC" => function ($q) {
                    $q->select("*");
                },
                "accompanimentsC" => function ($q) {
                    $q->select("*")
                        ->with('profiles');
                },
                "accompanimentsC.travelEstimationAccompanimentC" => function ($q) {
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
                "mpm_calculate_labor",
                "apm_calculate_labor",
                "c_apm_calculate_labor",
                "c_me_calculate_labor",
                "c_vd_calculate_labor",
            ]);

            $calculate_labor = request()->get("calculate_labor");
            $mpm_calculate_labor = request()->get("mpm_calculate_labor");
            $apm_calculate_labor = request()->get("apm_calculate_labor");
            $c_apm_calculate_labor = request()->get("c_apm_calculate_labor");
            $c_me_calculate_labor = request()->get("c_me_calculate_labor");
            $c_vd_calculate_labor = request()->get("c_vd_calculate_labor");

            ApuService::find($id)->update($data);

            $apuServiceDimensionalValidation =  ApuServiceDimensionalValidation::where("apu_service_id", $id)->get();
            foreach ($apuServiceDimensionalValidation as $value) {
                ApuServiceTravelEstimationDimensionalValidation::where("apu_service_dimensional_validation_id",  $value["id"])->delete();
            }
            ApuServiceDimensionalValidation::where("apu_service_id", $id)->delete();
            if ($calculate_labor) {
                foreach ($calculate_labor as $cl) {
                    $cl["apu_service_id"] = $id;
                    $dimsionalValid = ApuServiceDimensionalValidation::create($cl);
                    foreach ($cl["viatic_estimation"] as $value) {
                        $value["apu_service_dimensional_validation_id"] = $dimsionalValid["id"];
                        ApuServiceTravelEstimationDimensionalValidation::create($value);
                    }
                }
            }

            $apuServiceAssemblyStartUp =  ApuServiceAssemblyStartUp::where("apu_service_id", $id)->get();
            foreach ($apuServiceAssemblyStartUp as $value) {
                ApuServiceTravelEstimationAssemblyStartUp::where("apu_service_assembly_start_up_id",  $value["id"])->delete();
            }
            ApuServiceAssemblyStartUp::where("apu_service_id", $id)->delete();
            if ($mpm_calculate_labor) {
                foreach ($mpm_calculate_labor as $mpm_cl) {
                    $mpm_cl["apu_service_id"] = $id;
                    $assemblyStart = ApuServiceAssemblyStartUp::create($mpm_cl);
                    foreach ($mpm_cl["viatic_estimation"] as $value) {
                        $value["apu_service_assembly_start_up_id"] = $assemblyStart["id"];
                        ApuServiceTravelEstimationAssemblyStartUp::create($value);
                    }
                }
            }

            $apuServiceAccompaniment =  ApuServiceAccompaniment::where("apu_service_id", $id)->get();
            foreach ($apuServiceAccompaniment as $value) {
                ApuServiceTravelEstimationAccompaniment::where("apu_service_accompaniments_id",  $value["id"])->delete();
            }
            ApuServiceAccompaniment::where("apu_service_id", $id)->delete();
            if ($apm_calculate_labor) {
                foreach ($apm_calculate_labor as $apm_cl) {
                    $apm_cl["apu_service_id"] = $id;
                    $accompaniment = ApuServiceAccompaniment::create($apm_cl);
                    foreach ($apm_cl["viatic_estimation"] as $value) {
                        $value["apu_service_accompaniments_id"] = $accompaniment["id"];
                        ApuServiceTravelEstimationAccompaniment::create($value);
                    }
                }
            }

            $apuServiceAccompanimentContractor =  ApuServiceAccompanimentContractor::where("apu_service_id", $id)->get();
            foreach ($apuServiceAccompanimentContractor as $value) {
                ApuServiceTravelEstimationAccompanimentContractor::where("apu_service_accompaniment_contractors_id",  $value["id"])->delete();
            }
            ApuServiceAccompanimentContractor::where("apu_service_id", $id)->delete();
            if ($c_apm_calculate_labor) {
                foreach ($c_apm_calculate_labor as $c_amp_cl) {
                    $c_amp_cl["apu_service_id"] = $id;
                    $accompanimentC = ApuServiceAccompanimentContractor::create($c_amp_cl);
                    foreach ($c_amp_cl["viatic_estimation"] as $value) {
                        $value["apu_service_accompaniment_contractors_id"] = $accompanimentC["id"];
                        ApuServiceTravelEstimationAccompanimentContractor::create($value);
                    }
                }
            }

            $apuServiceAssemblyEquipmentContractor =  ApuServiceAssemblyEquipmentContractor::where("apu_service_id", $id)->get();
            foreach ($apuServiceAssemblyEquipmentContractor as $value) {
                ApuServiceTravelEstimationAssemblyEquipmentContractor::where("apu_service_assembly_equipment_contractor_id",  $value["id"])->delete();
            }
            ApuServiceAssemblyEquipmentContractor::where("apu_service_id", $id)->delete();
            if ($c_me_calculate_labor) {
                foreach ($c_me_calculate_labor as $c_me_cl) {
                    $c_me_cl["apu_service_id"] = $id;
                    $assemblyEquipentC = ApuServiceAssemblyEquipmentContractor::create($c_me_cl);
                    foreach ($c_me_cl["viatic_estimation"] as $value) {
                        $value["apu_service_assembly_equipment_contractor_id"] = $assemblyEquipentC["id"];
                        ApuServiceTravelEstimationAssemblyEquipmentContractor::create($value);
                    }
                }
            }

            $apuServiceDimensionalValidationContractors =  ApuServiceDimensionalValidationContractors::where("apu_service_id", $id)->get();
            foreach ($apuServiceDimensionalValidationContractors as $value) {
                ApuServiceTravelEstimationDimensionalValidationContractors::where("apu_service_dimensional_validation_contractors_id",  $value["id"])->delete();
            }
            ApuServiceDimensionalValidationContractors::where("apu_service_id", $id)->delete();
            if ($c_vd_calculate_labor) {
                foreach ($c_vd_calculate_labor as $c_vd_cl) {
                    $c_vd_cl["apu_service_id"] = $id;
                    $dimsionalValidC = ApuServiceDimensionalValidationContractors::create($c_vd_cl);
                    foreach ($c_vd_cl["viatic_estimation"] as $value) {
                        $value["apu_service_dimensional_validation_contractors_id"] = $dimsionalValidC["id"];
                        ApuServiceTravelEstimationDimensionalValidationContractors::create($value);
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
        $image = $company->page_heading;
        $data = ApuService::with([
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
            ->first();
        //return $data;
        $datosCabecera = (object) array(
            'Titulo' => 'APU Servicio',
            'Codigo' => $data->code,
            'Fecha' => $data->created_at,
            'CodigoFormato' => $data->format_code
        );
        $pdf = PDF::loadView('pdf.apu_service', [
            'data' => $data,
            'company' => $company,
            'datosCabecera' => $datosCabecera,
            'image' => $image
        ]);
        return $pdf->setPaper('A4', 'landscape')->download('apu_service');
    }
}
