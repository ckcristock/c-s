<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\CentroCostoWorkOrder;
use App\Models\WorkOrder;
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
        $data = $request->all();
        if (!$request->id) {
            $data['code'] = generateConsecutive('work_orders', 'BGA');
        }
        $updateOrCreate = WorkOrder::updateOrCreate(['id' => $request->id], $data);
        if ($updateOrCreate->wasRecentlyCreated) {
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
        $updateOrCreate->wasRecentlyCreated ? sumConsecutive('work_orders') : '';
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
        return $this->success(
            WorkOrder::where('id', $id)
                ->with(
                    'third_party',
                    'third_party_person',
                    'quotation',
                    'blueprints',
                    'city:*,id,name as text,id as value'
                )
                ->first()
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
