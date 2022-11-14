<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return $this->success(Measure::where('state', 'Activo')->get(['measure', 'name As text', 'id As value']));
    }

    public function measureActive(){
        $measure = Measure::where('state', 'Activo')->get(['measure', 'name As text', 'id As value']);
        return $this->success($measure);
    }

    public function paginate()
    {
        /* return $this->success(
            Measure::paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        ); */
        return $this->success(
            Measure::orderBy('state')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
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
            $measure = Measure::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($measure->wasRecentlyCreated)
                ?
                $this->success([
                    'title' => '¡Creado con éxito!',
                    'text' => 'La medida ha sido creada satisfactoriamente'
                ])
                :
                $this->success([
                    'title' => '¡Actualizado con éxito!',
                    'text' => 'La medida ha sido actualizada satisfactoriamente'
                ]);
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
    public function changeState(Request $request)
    {
        try {
            $measure = Measure::find(request()->get('id'));
            $measure->state = request()->get('state');
            $measure->save();
            return $this->success('Proceso Correcto');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }
}
