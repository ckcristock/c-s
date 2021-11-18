<?php

namespace App\Http\Controllers;

use App\Models\Thickness;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ThicknessController extends Controller
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
            Thickness::all()
        );
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
            $thickness = Thickness::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($thickness->wasRecentlyCreated)
            ?
            $this->success([
            'title' => '¡Creado con éxito!',
            'text' => 'El Espesor ha sido creado satisfactoriamente'
            ])
            :
            $this->success([
            'title' => '¡Actualizado con éxito!',
            'text' => 'El Espesor ha sido actualizado satisfactoriamente'
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

}
