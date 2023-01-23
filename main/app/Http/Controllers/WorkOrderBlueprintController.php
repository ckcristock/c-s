<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderBlueprint;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class WorkOrderBlueprintController extends Controller
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
        $data = $request->except('file_base64');
        $base64 = saveBase64FileWorkOrders($request->file_base64, $request->set_name, $request->work_order_id);
        $name = URL::to('/') . '/api/file?path=' . $base64;
        dd($name);
        WorkOrderBlueprint::create($data);
        return $this->success('Plano agregado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrderBlueprint  $workOrderBlueprint
     * @return \Illuminate\Http\Response
     */
    public function show(WorkOrderBlueprint $workOrderBlueprint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkOrderBlueprint  $workOrderBlueprint
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrderBlueprint $workOrderBlueprint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrderBlueprint  $workOrderBlueprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrderBlueprint $workOrderBlueprint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkOrderBlueprint  $workOrderBlueprint
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderBlueprint $workOrderBlueprint)
    {
        //
    }
}
