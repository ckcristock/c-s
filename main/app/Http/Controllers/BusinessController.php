<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Business;
use App\Models\BusinessBudget;
use App\Models\BusinessQuotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Milion\Barcode\BarcodeGenerator;

class BusinessController extends Controller
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
            Business::with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget')->get()
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

    public function paginate(Request $request)
    {
        return $this->success(
            Business::with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget')
                ->orderByDesc('date')
                ->when($request->name, function ($q, $fill) {
                    $q->where('name', 'like', "%$fill%");
                })
                ->when($request->code, function ($q, $fill) {
                    $q->where('code', 'like', "%$fill%");
                })
                ->when($request->company_name, function ($q, $fill) {
                    return $q->whereHas('thirdParty', function($q) use($fill){
                        $q->where('social_reason', 'like', "%$fill%")
                        ->orWhereRaw("CONCAT_WS(' ', first_name, first_surname) = '%$fill%'");
                    });
                })
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $quotations = $request->quotations;
            $business = Business::create($request->except('budgets'));
            $business['code'] = '#NZ' . $business->id;
            $business->save();
            if ($quotations) {
                foreach ($quotations as $key => $value) {
                    BusinessQuotation::create([
                        'quotation_id' => $value['id'],
                        'business_id' =>  $business->id
                    ]);
                }
            }
            if ($request->get('budgets')) {
                foreach ($request->get('budgets') as $budget) {
                    BusinessBudget::create([
                        'budget_id' => $budget['id'],
                        'business_id' =>  $business->id
                    ]);
                }
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function newBusinessBudget(Request $request)
    {
        try {
            Business::where('id', $request->business_id)->update(['budget_value' => $request->budget_value]);
            foreach ($request->get('budgets') as $budget) {
                BusinessBudget::create([
                    'budget_id' => $budget['budget_id'],
                    'business_id' =>  $budget['business_budget_id']
                ]);
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function newBusinessQuotation(Request $request)
    {
        try {
            $business = Business::where('id', $request->business_id)->first();
            $business->update(['quotation_value' => $request->quotation_value]);
            foreach ($request->quotations as $quotation) {
                BusinessQuotation::create([
                    'quotation_id' => $quotation['quotation_id'],
                    'business_id' =>  $quotation['business_id']
                ]);
            }
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
        $business = Business::where('id', $id)
            ->with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget', 'quotations')
            ->first();
        $codeQR = new DNS2D();
        $business2 = Business::where('id', $id)->first();
        $codeQRc = $codeQR->getBarcodePNG(json_encode($business2), 'QRCODE', 10, 10);
        return $this->success(
            $business, $codeQRc
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
            Business::find($id)->update($request->all());
            return $this->success('Estado cambiado');
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

    public function getTasks($id)
    {
        return $this->success(Business::with('tasks')->where('id', $id)->first());
    }
}
