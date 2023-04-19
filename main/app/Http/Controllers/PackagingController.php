<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PackagingController extends Controller
{

    use ApiResponser;

    public function paginate() {
        return $this->success(
            Packaging::paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            Packaging::get(['id as value', 'name as text'])
        );
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
        $createOrUpdate = Packaging::updateOrCreate(['id' => $request->id],$request->all());
        return $this->success($createOrUpdate->wasRecentlyCreated ? 'Creado con éxito' : 'Actualizado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\Response
     */
    public function show(Packaging $packaging)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\Response
     */
    public function edit(Packaging $packaging)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Packaging $packaging)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Packaging  $packaging
     * @return \Illuminate\Http\Response
     */
    public function destroy(Packaging $packaging)
    {
        //
    }
}
