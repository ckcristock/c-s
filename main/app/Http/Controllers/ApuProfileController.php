<?php

namespace App\Http\Controllers;

use App\Models\ApuProfile;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ApuProfileController extends Controller
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
            ApuProfile::where('state', 'Activo')
                ->when(!request()->get('full'), function ($q, $fill) {
                    $q->where('id', '!=', 1);
                })->get()
        );
    }

    public function paginate()
    {
        return $this->success(
            ApuProfile::when(request()->get('profile'), function ($q, $fill) {
                $q->where('profile', 'like', '%' . $fill . '%');
            })
                ->where('id', '!=', 1)
                ->orderBy('profile')
                ->paginate(request()->get('pageSize', 50), ['*'], 'page', request()->get('page', 1))
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
            $apuProfile = ApuProfile::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($apuProfile->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
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
