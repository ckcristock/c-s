<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Person;
use App\Models\WorkOrder;
use App\Models\WorkOrderEngineering;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkOrderEngineeringController extends Controller
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
        $work_order_engineering = WorkOrderEngineering::with('work_order', 'person', 'allocator_person')
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
            ->when($request->person_id, function ($q, $fill) {
                $q->where('person_id', $fill);
            })
            ->orderByRaw("FIELD(status , 'pendiente', 'proceso', 'completado') ASC")
            ->orderByDesc('created_at')
            ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1));
        return $this->success($work_order_engineering);
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
        //crear tarea
        $data = $request->all();
        $data['allocator_person_id'] = auth()->user()->person_id;
        $data['person_id'] = $request->person_id['value'];
        $work_order_engineering = WorkOrderEngineering::create($data);
        $work_order = WorkOrder::where('id', $data['work_order_id'])->first();
        $work_order->update(['status' => 'ingenieria']);
        createAlert($data, $work_order_engineering->id, ' te ha asignado la orden de producción ', $work_order, 'fas fa-tools', 'Orden de producción (ingeniería)');
        //createTask($data);
        return $this->success('Hemos enviado una notificación al funcionario informándole que le has asignado una orden de producción.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrderEngineering  $workOrderEngineering
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(WorkOrderEngineering::where('id', $id)->with('work_order.quotation', 'person', 'allocator_person')->first());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkOrderEngineering  $workOrderEngineering
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrderEngineering $workOrderEngineering)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrderEngineering  $workOrderEngineering
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $now = Carbon::now()->toDateTimeString();
        $woe = WorkOrderEngineering::where('id', $id)->first();
        if ($request->status == 'proceso') {
            $data['start_time'] = $now;
        } else if ($request->status == 'completado') {
            $data['end_time'] = $now;
            //WorkOrder::where('id', $woe->work_order_id)->first()->update(['status' => 'diseño']);
        }
        $woe->update($data);
        return $this->success('Estado cambiado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkOrderEngineering  $workOrderEngineering
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderEngineering $workOrderEngineering)
    {
        //
    }
}
