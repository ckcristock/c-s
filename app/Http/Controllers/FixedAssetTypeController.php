<?php

namespace App\Http\Controllers;

use App\Models\FixedAssetType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class FixedAssetTypeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            FixedAssetType::all(['name as text', 'id as value'])
        );
    }

    public function paginate()
    {
        return $this->success(
            FixedAssetType::when( Request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->when( Request()->get('category') , function($q, $fill)
            {
                $q->where('category','like','%'.$fill.'%');
            })
            ->when( Request()->get('useful_life_niif') , function($q, $fill)
            {
                $q->where('useful_life_niif','like','%'.$fill.'%');
            })
            ->when( Request()->get('depreciation') , function($q, $fill)
            {
                $q->where('annual_depreciation_percentage_niif','like','%'.$fill.'%');
            })
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
        try {
            return $this->success(
                FixedAssetType::updateOrCreate(['id' => $request->get('id')], $request->all())
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
