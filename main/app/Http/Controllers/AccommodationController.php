<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Accommodation;

class AccommodationController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Accommodation::all());
    }

    public function paginate()
	{
		return $this->success(
			Accommodation:://with('hotels')
            withTrashed()
			->when( request()->get('name') , function($q, $fill)
			{
				$q->where('name','like','%'.$fill.'%');
			})
			->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1))
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
            $nuevo = Accommodation::updateOrCreate($request->all());
            return ($nuevo->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile(),204);
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
        return $this->success(Accommodation::find($id));
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
        return $this->success('Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $register = Accommodation::find($id);
            $register->delete();
            if ($register) {
                return $this->success('Registro eliminado exitosamente');
            } else {
                return $this->error('Occurrió un error al intentar borrar',204);
            }
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile(),204);
        }
    }

    /**
     * Handle the User "restored" event.
     *
     * @param   $instance
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        try {
            //dd($request);
            $data = $request->all()['data'];
            //return $data;
            $register = Accommodation::withTrashed()
                        ->where('id' ,$data['id'])
                        ->restore();
//                        dd($register);
            if ($register) {
                return $this->success('Registro restaurado exitosamente');
            } else {
                return $this->error('Occurrió un error al intentar activar',204);
            }
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile(),204);
        }
    }
}
