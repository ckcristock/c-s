<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HotelController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		try {
			$nacional = Hotel::with('city')->where('type', '=', 'Nacional')->get();
			$internacional = Hotel::where('type', '=', 'Internacional')->get();

			return $this->success(['nacional' => $nacional, 'internacional' => $internacional]);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 400);
		}
	}

	public function paginate()
	{
		return $this->success(
			Hotel::orderBy('type')
            ->with('city')
			->when( request()->get('tipo') , function($q, $fill)
			{
				$q->where('type','=',$fill);
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
			$nuevo = Hotel::updateOrCreate(['id' => $request->get('id')],[
                'type' => $request->type,
                'name' => $request->name,
                'address' => $request->address,
                //'rate' => $request->rate,
                'phone' => $request->phone,
                'landline' => $request->landline,
                'city_id' => $request->city_id,
                'simple_rate' => $request->simple_rate,
                'double_rate' => $request->double_rate,
                'breakfast' => $request->breakfast,
                'accommodation' => $request->accommodation
            ]);
            dd($nuevo);

			return $this->success('Creado con éxito');
		} catch (\Throwable $th) {
            //return $th;
			return $this->error($th->getMessage(). ' msg: ' . $th->getLine() . ' ' . $th->getFile(), 500);
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
