<?php

namespace App\Http\Controllers;

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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function paginate(Request $request)
    {
        return $this->success(
            WorkOrder::with('city', 'third_party', 'third_party_person')
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
                /* ->when($request->status, function ($q, $fill) {
                    $q->where('status', $fill);
                }) */
                ->when($request->start_date, function ($q, $fill) use ($request) {
                    $q->whereBetween('date', [$fill, $request->date_end]);
                })
                ->when($request->start_delivery_date, function ($q, $fill) use ($request) {
                    $q->whereBetween('delivery_date', [$fill, $request->end_delivery_date]);
                })
                ->orderByDesc('created_at')
                ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
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
        $wo = WorkOrder::with('city', 'third_party', 'third_party_person')
        ->when($request->status, function ($q, $fill) {
            $q->where('status', $fill);
        })
        ->orderBy('delivery_date', strval($request->orderBy))
        ->get();
        return $this->success($wo);
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
