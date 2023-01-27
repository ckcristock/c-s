<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderDesign;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkOrderDesignController extends Controller
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
        $work_order_design = WorkOrderDesign::with('work_order', 'people', 'allocator_person')
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
                $q->whereHas('people', function ($query) use ($fill) {
                    $query->where('people.id', $fill);
                });
            })
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
        //crear tarea
        $data = $request->except('people');
        $people = $request->people;

        $data['allocator_person_id'] = auth()->user()->id;
        $work_order_design = WorkOrderDesign::create($data);
        $work_order = WorkOrder::where('id', $data['work_order_id'])->first();
        $work_order_design->people()->attach($people);
        $work_order->update(['status' => 'diseño']);
        for ($i = 0; $i < count($people); $i++) {
            $data['person_id'] = $people[$i];
            createAlert($data, $work_order_design->id, ' te ha asignado la orden de producción ', $work_order, 'fas fa-tools', 'Orden de producción (diseño)');
        }
        //createTask($data);
        return $this->success('Hemos enviado una notificación al (los) funcionario(s) informándole(s) que le(s) has asignado una orden de producción.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrderDesign  $workOrderDesign
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(WorkOrderDesign::where('id', $id)->with('work_order.quotation', 'people', 'allocator_person')->first());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkOrderDesign  $workOrderDesign
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrderDesign $workOrderDesign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrderDesign  $workOrderDesign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $now = Carbon::now()->toDateTimeString();
        $wod = WorkOrderDesign::where('id', $id)->first();
        if ($request->status == 'proceso') {
            $data['start_time'] = $now;
        } else if ($request->status == 'completado') {
            $data['end_time'] = $now;
            //WorkOrder::where('id', $wod->work_order_id)->first()->update(['status' => 'produccion']);
        }
        $wod->update($data);
        return $this->success('Estado cambiado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkOrderDesign  $workOrderDesign
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderDesign $workOrderDesign)
    {
        //
    }
}
