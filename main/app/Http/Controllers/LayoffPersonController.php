<?php

namespace App\Http\Controllers;

use App\Models\LayoffPerson;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class LayoffPersonController extends Controller
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
     * @param  \App\Models\LayoffPerson  $layoffPerson
     * @return \Illuminate\Http\Response
     */
    public function show(LayoffPerson $layoffPerson)
    {
        return $this->success('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LayoffPerson  $layoffPerson
     * @return \Illuminate\Http\Response
     */
    public function edit(LayoffPerson $layoffPerson)
    {
        return $this->success('edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LayoffPerson  $layoffPerson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LayoffPerson $layoffPerson)
    {
        return $this->success('update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LayoffPerson  $layoffPerson
     * @return \Illuminate\Http\Response
     */
    public function destroy(LayoffPerson $layoffPerson)
    {
        return $this->success('destroy');
    }
}
