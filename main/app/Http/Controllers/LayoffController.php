<?php

namespace App\Http\Controllers;

use App\Models\Layoff;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class LayoffController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success('index');
    }

    public function paginate(Request $request)
    {
        return $this->success('paginated');
    }

    public function getCheckLayoffsList()
    {
        return $this->success('getCheckList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->success('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->success('store');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function show(Layoff $layoff)
    {
        return $this->success('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function edit(Layoff $layoff)
    {
        return $this->success('edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layoff $layoff)
    {
        return $this->success('update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Layoff  $layoff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layoff $layoff)
    {
        return $this->success('destroy');
    }
}
