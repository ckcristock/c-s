<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Business;
use App\Models\BusinessBudget;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business = Business::create($request->except('budgets'));
            $business['code'] = '#NZ' . $business->id;
            $business->save();
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
            Business::where('id',$request->business_id)->update(['budget_value' => $request->budget_value]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(
            Business::where('id',$id)->with('thirdParty', 'thirdPartyPerson', 'country', 'city', 'businessBudget')->first()
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
