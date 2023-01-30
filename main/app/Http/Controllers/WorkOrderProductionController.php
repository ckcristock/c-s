<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderProduction;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class WorkOrderProductionController extends Controller
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

    public function paginate(Request $request)
    {
        $work_order_design = WorkOrderProduction::with('work_order', 'allocator_person')
            ->when($request->code, function ($q, $fill) {
                $q->whereHas('work_order', function ($query) use ($fill) {
                    $query->where('code', 'like', "%$fill%");
                });
            })
            ->when($request->status, function ($q, $fill) {
                $q->where('status', $fill);
            })
            ->when($request->allocator_person_id, function ($q, $fill) {
                $q->where('allocator_person_id', $fill);
            })
            /* ->when($request->person_id, function ($q, $fill) {
                $q->whereHas('people', function ($query) use ($fill) {
                    $query->where('people.id', $fill);
                });
            }) */
            ->orderByRaw("FIELD(status , 'pendiente', 'proceso', 'completado') ASC")
            ->orderByDesc('created_at')
            ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1));
        return $this->success($work_order_design);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrderProduction  $workOrderProduction
     * @return \Illuminate\Http\Response
     */
    public function show(WorkOrderProduction $workOrderProduction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkOrderProduction  $workOrderProduction
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrderProduction $workOrderProduction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrderProduction  $workOrderProduction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrderProduction $workOrderProduction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkOrderProduction  $workOrderProduction
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderProduction $workOrderProduction)
    {
        //
    }
}
