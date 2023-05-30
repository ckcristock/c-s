<?php

namespace App\Http\Controllers;

use App\Models\ApuPart;
use App\Models\ApuService;
use App\Models\ApuSet;
use App\Models\Budget;
use App\Models\Business;
use App\Models\CentroCosto;
use App\Models\CentroCostoWorkOrder;
use App\Models\Municipality;
use App\Models\Quotation;
use App\Models\WorkOrder;
use App\Models\WorkOrderElement;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(WorkOrder::get(['id', 'id as value', 'code as text']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function paginate(Request $request)
    {
        $pageSize = $request->get('pageSize', 10);
        $page = $request->get('page', 1);

        $workOrdersQuery = WorkOrder::with('city.department_', 'third_party', 'third_party_person')
            ->when($request->city, function ($q, $fill) {
                $q->whereHas('city', function ($query) use ($fill) {
                    $query->where('name', 'like', "%$fill%");
                });
            })
            ->when($request->client, function ($q, $fill) {
                $q->whereHas('client', function ($query) use ($fill) {
                    $query->where('social_reason', 'like', "%$fill%");
                });
            })
            ->when($request->code, function ($q, $fill) {
                $q->where('code', 'like', "%$fill%");
            })
            ->when($request->description, function ($q, $fill) {
                $q->where('description', 'like', "%$fill%");
            })
            ->when($request->observation, function ($q, $fill) {
                $q->where('observation', 'like', "%$fill%");
            })
            ->when($request->start_date, function ($q, $fill) use ($request) {
                $q->whereBetween('date', [$fill, $request->date_end]);
            })
            ->when($request->start_delivery_date, function ($q, $fill) use ($request) {
                $q->whereBetween('delivery_date', [$fill, $request->end_delivery_date]);
            })
            ->orderByDesc('created_at');

        $workOrders = $workOrdersQuery->paginate($pageSize, ['*'], 'page', $page);

        return $this->success($workOrders);
    }


    public function getLastId()
    {
        return $this->success(WorkOrder::all()->last());
    }


    public function create()
    {
        //
    }

    public function forStage(Request $request)
    {
        $workOrders = WorkOrder::with('city', 'third_party', 'third_party_person', 'engineering')
            ->when($request->status, function ($query, $status) {
                if ($status == 'ingenieria') {
                    $query->where('status', $status);
                    $query->whereHas('engineering', function ($query) {
                        $query->where('status', 'completado');
                    });
                } else if ($status == 'diseño') {
                    $query->where('status', $status);
                    $query->whereHas('design', function ($query) {
                        $query->where('status', 'completado');
                    });
                } else if ($status == 'inicial') {
                    $query->where('status', $status);
                }
            })
            ->orderBy('delivery_date', strval($request->orderBy))
            ->get();

        return $this->success($workOrders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('apu_parts', 'apu_services', 'apu_sets', 'budgets', 'business', 'quotations');
        $apu_parts = $request->apu_parts;
        $apu_services = $request->apu_services;
        $apu_sets = $request->apu_sets;
        $budgets = $request->budgets;
        $business = $request->business;
        $quotations = $request->quotations;
        $consecutive = getConsecutive('work_orders');

        if (!$request->id) {
            if ($consecutive->city) {
                $abbreviation = Municipality::where('id', $data['municipality_id'])->first()->abbreviation;
                $data['code'] = generateConsecutive('work_orders', $abbreviation);
            } else {
                $data['code'] = generateConsecutive('work_orders');
            }
        }
        $updateOrCreate = WorkOrder::updateOrCreate(['id' => $request->id], $data);
        if ($updateOrCreate->wasRecentlyCreated) {
            foreach ($apu_parts as $apu_part) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $apu_part['id'],
                    'work_orderable_type' => ApuPart::class,
                ]);
            }

            foreach ($apu_services as $apu_service) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $apu_service['id'],
                    'work_orderable_type' => ApuService::class,
                ]);
            }

            foreach ($apu_sets as $apu_set) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $apu_set['id'],
                    'work_orderable_type' => ApuSet::class,
                ]);
            }

            foreach ($budgets as $budget) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $budget['id'],
                    'work_orderable_type' => Budget::class,
                ]);
            }

            foreach ($business as $business) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $business['id'],
                    'work_orderable_type' => Business::class,
                ]);
            }

            foreach ($quotations as $quotation) {
                WorkOrderElement::create([
                    'work_order_id' => $updateOrCreate->id,
                    'work_orderable_id' => $quotation['id'],
                    'work_orderable_type' => Quotation::class,
                ]);
            }

            sumConsecutive('work_orders');
            $centroCosto = CentroCosto::create([
                'Nombre' => $updateOrCreate->name,
                'Codigo' => '121' . $updateOrCreate->code,
                'Id_Centro_Padre' => 4,
                'Id_Tipo_Centro' => 3,
                'Movimiento' => 'Si',
                'company_id' => 1,
            ]);
            CentroCostoWorkOrder::create([
                'centro_costo_id' => $centroCosto->Id_Centro_Costo,
                'work_order_id' => $updateOrCreate->id
            ]);
        }
        return $this->success($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $workOrder = WorkOrder::with('third_party', 'third_party_person', 'quotation', 'blueprints', 'elements', 'city:*,id,name as text,id as value')
        ->find($id);
        return $this->success(
            $workOrder
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrder $workOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $workOrder->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrder $workOrder)
    {
        //
    }
}
