<?php

namespace App\Http\Controllers;

use App\Models\FixedTurn;
use App\Models\FixedTurnHour;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixedTurnController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return $this->success(FixedTurn::all(["id as value", "name as text"]));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $req)
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		//

		$fixedTurnData = $req->except("days");
		$fixed = FixedTurn::create($fixedTurnData);
		$hours = $req->get("days");
		$fixed->horariosTurnoFijo()->createMany($hours);
		return $this->success("creado con éxito");
		try {
		} catch (\Throwable $err) {
			return $this->error($err->getMessage(), 500);
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
		try {
			return $this->success(FixedTurn::where('id', $id)->with("horariosTurnoFijo")->first());
		} catch (\Throwable $err) {
			return $this->error($err->getMessage(), 500);
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
