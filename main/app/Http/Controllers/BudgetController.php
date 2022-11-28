<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetIndirectCost;
use App\Models\BudgetItem;
use App\Models\BudgetItemSubitem;
use App\Models\BudgetItemSubitemIndirectCost;
use App\Models\Company;
use App\Models\BusinessTask;
use App\Services\BudgetService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

use function JmesPath\search;

class BudgetController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->success(
            Budget::when($request->destinity_id, function ($q, $fill) {
                $q->where('destinity_id', $fill);
            })->when($request->customer_id, function ($q, $fill) {
                $q->where('customer_id', $fill);
            })->with(
                [
                    'destiny' => function ($q) {
                        $q->select('*');
                    },
                    'user' => function ($q) {
                        $q->select('id', 'usuario', 'person_id')
                            ->with(
                                ['person' => function ($q) {
                                    $q->select('id', 'first_name', 'first_surname');
                                }]
                            );;
                    },
                    'customer' => function ($q) {
                        $q->select('id', 'nit')
                            ->selectRaw('IFNULL(social_reason, CONCAT_WS(" ",first_name, first_name) ) as name');
                    }
                ]
            )->get('*', 'id as value', 'line as text')
        );
    }
    public function paginate()
    {
        return $this->success(
            Budget::with(
                [
                    'destiny' => function ($q) {
                        $q->select('*');
                    },
                    'user' => function ($q) {
                        $q->select('id', 'usuario', 'person_id')
                            ->with(
                                ['person' => function ($q) {
                                    $q->select('id', 'first_name', 'first_surname');
                                }]
                            );;
                    },
                    'customer' => function ($q) {
                        $q->select('id', 'nit')
                            ->selectRaw('IFNULL(social_reason, CONCAT_WS(" ",first_name, first_name) ) as name');
                    }
                ]
            )->orderBy('id', 'desc')
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function saveTask(Request $request)
    {
        /*  $data = $request->get('data');

        $businesstask = BusinessTask::create($data);
*/
        try {
            BusinessTask::updateOrCreate(['id' => $request->get('id')], $request->all());
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
        $data = $request->get('data');
        $data['user_id'] = auth()->user()->id;

        $budgetDb = Budget::create($data);

        foreach ($data['indirect_costs'] as $indirectCost) {
            $indirectCost['budget_id'] = $budgetDb->id;
            BudgetIndirectCost::create($indirectCost);
        }
        foreach ($data['items'] as $item) {
            $item['budget_id'] = $budgetDb->id;


            $itemDb =  BudgetItem::create($item);

            foreach ($item['subItems'] as $subitem) {

                $subitem['budget_item_id'] = $itemDb->id;
                $subitem['type_module'] == 'apu_part' ?  $subitem['apu_part_id'] = $subitem['apu_id'] : '';
                $subitem['type_module'] == 'apu_set' ?  $subitem['apu_set_id'] = $subitem['apu_id'] : '';
                $subitem['type_module'] == 'service' ?  $subitem['service_id'] = $subitem['apu_id'] : '';

                $subItemDB = BudgetItemSubitem::create($subitem);

                foreach ($subitem['indirect_costs'] as $indirect_cost) {
                    # code...
                    $indirect_cost['budget_item_subitem_id'] = $subItemDB->id;
                    BudgetItemSubitemIndirectCost::create($indirect_cost);
                }
            }
        }

        return $this->success($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return $this->success(BudgetService::show($id));
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
        $data = $request->all();

        $budgetDb = Budget::find($id);

        DB::table('budget_indirect_costs')->where('budget_id', $id)->delete();

        foreach ($data['indirect_costs'] as $indirectCost) {
            $indirectCost['budget_id'] = $budgetDb->id;
            BudgetIndirectCost::create($indirectCost);
        }


        if ($data['itemsTodelete'] && count($data['itemsTodelete']) > 0) {
            BudgetService::deleteItems($data['itemsTodelete']);
        }

        if ($data['subItemsToDelete'] && count($data['subItemsToDelete']) > 0) {
            BudgetService::deleteSubItems($data['subItemsToDelete']);
        }

        $budgetDb = Budget::find($id);
        $budgetDb->update($request->except(['indirect_costs', 'subtotal_indirect_cost_dynamic', 'items']));



        foreach ($data['items'] as $item) {
            $item['budget_id'] = $budgetDb->id;
            unset($item['shows']);
            ##unset($item['subItems']);
            unset($item['subtotal_indirect_cost_dynamic']);

            # dd(['id' => $item['id'], $item]);


            $itemDb =  BudgetItem::updateOrCreate(['id' => $item['id']], $item);
            # AccountPlan::updateOrCreate(['id' => $request->get('id')], $request->all());
            foreach ($item['subItems'] as $subitem) {
                $subitem['budget_item_id'] = $itemDb->id;
                $subitem['type_module'] == 'apu_part' ?  $subitem['apu_part_id'] = $subitem['apu_id'] : '';
                $subitem['type_module'] == 'apu_set' ?  $subitem['apu_set_id'] = $subitem['apu_id'] : '';
                $subitem['type_module'] == 'service' ?  $subitem['service_id'] = $subitem['apu_id'] : '';

                $subItemDB = BudgetItemSubitem::updateOrCreate(['id' => $subitem['id']], $subitem);
            }
        }

        return response(22);
    }


    private function search()
    {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadClient(Request $request)
    {
        //
        $data = BudgetService::show($request->get('id'));
        $company = Company::first();
        $currency = $request->get('currency');
        //return view('pdf.presupuesto_cliente',  compact('currency', 'data', 'company'));
        $pdf = PDF::loadView('pdf.presupuesto_cliente', compact('currency', 'data', 'company'));
        return $pdf->download('presupuesto_cliente.pdf');
    }
}
