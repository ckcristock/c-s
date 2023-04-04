<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CustomFacades\ImgUploadFacade;
use Illuminate\Support\Facades\URL;

class CompanyController extends Controller
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
        return $this->success(Company::all(['id as value', 'name as text']));
    }
    public function getBasicDataForId($id)
    {
        $data = Company::where('id', $id)->get();
        return $this->success($data);
    }
    public function getAllCompanies()
    {
        return $this->success(Company::all());
    }
    public function getBasicData()
    {
        return $this->success(
            Company::with('arl')->with('bank', 'payCompanyConfiguration')->first()
        );
    }

    public function saveCompanyData(Request $request)
    {
        $differences = findDifference($request->all(), Company::class);
        $company = Company::findOrFail($request->get('id'));
        $company_data = $request->all();
        if ($request->has('logo')) {
            if ($company_data['logo'] != $company->logo) {
                $company_data['logo'] = URL::to('/') . '/api/image?path=' . saveBase64($company_data['logo'], 'company/');
            }
        }
        if ($request->has('page_heading')) {
            $company_data['page_heading'] = URL::to('/') . '/api/image?path=' . saveBase64($company_data['page_heading'], 'company/');
        }
        $company->update($company_data);
        
        saveHistoryCompanyData($differences, Company::class);
        return $this->success('');
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


    public function commercialTermsSave(Request $request, $id)
    {
        try {
            Company::find($id)->update($request->all());
            return $this->success('Actualizado correctamente');
        } catch (\Throwable $th) {
        }
    }

    public function getTexts(Request $request)
    {
        try {
            return $this->success(
                Company::where('id', 1)->first(['commercial_terms', 'legal_requirements', 'technical_requirements'])
            );
        } catch (\Throwable $th) {
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $typeImage = '.' . $request->typeImage;
        $data["logo"] = URL::to('/') . '/api/image?path=' . saveBase64($data['logo'], 'companies/', true, $typeImage);
        try {
            WorkContract::find($request->get('id'))->update($data);
            return response()->json(['message' => 'Se ha actualizado con éxito']);
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

    public function getGlobal()
    {
        return Company::with('payConfiguration')->with('arl')->first([
            'id', 'arl_id', 'payment_frequency', 'social_reason', 'document_number',
            'transportation_assistance', 'base_salary', 'law_1607'
        ]);
    }
}
