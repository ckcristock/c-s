<?php

namespace App\Http\Controllers;

use App\Models\HistoryDataCompany;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class HistoryDataCompanyController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(HistoryDataCompany::with('Person')->get());
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
     * @param  \App\Models\HistoryDataCompany  $historyDataCompany
     * @return \Illuminate\Http\Response
     */
    public function show(HistoryDataCompany $historyDataCompany)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HistoryDataCompany  $historyDataCompany
     * @return \Illuminate\Http\Response
     */
    public function edit(HistoryDataCompany $historyDataCompany)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HistoryDataCompany  $historyDataCompany
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HistoryDataCompany $historyDataCompany)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HistoryDataCompany  $historyDataCompany
     * @return \Illuminate\Http\Response
     */
    public function destroy(HistoryDataCompany $historyDataCompany)
    {
        //
    }
}
