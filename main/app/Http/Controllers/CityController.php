<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return $this->success(City::where('state', '=', 'Activo')
			->when(Request()->get('name'), function ($q, $fill) {
				$q->where('name', 'like', "%$fill%");
			})
			->when(Request()->get('country'), function ($q, $fill) {
				$q->where('country_id', '=', "$fill");
			})
			->get(['*', 'id as value', 'name as text']));
	}

	public function getCitiesCountry($idCountry){
		return $this->success(City::where('state', '=', 'Activo')
		->where('country_id', '=', "$idCountry")
		->get(['*', 'id as value', 'name as text']));
	}

	public function paginate()
	{
		return $this->success(
			DB::table('cities as c')
				->select(
					'c.id',
					'c.name',
					'cs.id as country_id',
					'cs.name as country',
					'c.percentage_product',
					'c.percentage_service',
					'c.state'
				)
				->when(request()->get('name'), function ($q, $fill) {
					$q->where('c.name', 'like', '%' . $fill . '%');
				})
				->when(request()->get('country'), function ($q, $fill) {
					$q->where('c.country_id', 'like', '%' . $fill . '%');
				})
				->join('countries as cs', 'cs.id', '=', 'c.country_id')
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
			$city = City::updateOrCreate(['id' => $request->get('id')], $request->all());
			return ($city->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}

	/**
	 * Display the specified resource.
     * Muestra las ciudades dado el id del Departamento/Estado
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
        $cities = City::where('department_id', $id)
                        ->orderBy('name', 'asc')
                      ->get(['name as text', 'id as value']);
        if (count($cities)===0) {
            return $this->error('Ciudades no encontrados para este departamento/estado', 204);
        }else{
            return $this->success(
                $cities
                //Department::where('country_id', $country_id)->get()
            );
        }
	}

    	/**
	 * Display the specified resource.
     * Muestra las ciudades dado el id del Municipio
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showByMunicipality($id)
	{
        $cities = City::where('municipality_id', $id)
                      ->get(['name as text', 'id as value']);
        if (count($cities)===0) {
            return $this->error('Ciudades no encontrados para este municipio', 204);
        }else{
            return $this->success(
                $cities
                //Department::where('country_id', $country_id)->get()
            );
        }
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
