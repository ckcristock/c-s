<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderEngineering;
use App\Traits\ApiResponser;
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
        $data = $request->all();
        $data['allocator_person_id'] = auth()->user()->id;
        $data['person_id'] = $request->person_id['value'];
        WorkOrderEngineering::create($data);
        return $this->success($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkOrderEngineering  $workOrderEngineering
     * @return \Illuminate\Http\Response
     */
    public function show(WorkOrderEngineering $workOrderEngineering)
    {
        //
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
    public function update(Request $request, WorkOrderEngineering $workOrderEngineering)
    {
        //
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
