<?php

namespace App\Http\Controllers;

use App\Models\TravelExpense;
use App\Models\TravelExpenseFeeding;
use App\Models\TravelExpenseHotel;
use App\Models\TravelExpenseTaxiCity;
use App\Models\TravelExpenseTransport;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TravelExpenseController extends Controller
{
	use ApiResponser;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		return $this->success(
			TravelExpense::with("destiny")
				->with("origin")
				->with("user")
				->with("user.person")
				->with("person")
				->get()
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
		$totals = $request->except([
			"hospedaje",
			"taxi",
			"feeding",
			"transporte",
			"travel",
		]);
		$hotels = request()->get("hospedaje");
		$taxis = request()->get("taxi");
		$feedings = request()->get("feeding");
		$transports = request()->get("transporte");
		$travelExpense = request()->get("travel");
		$travelExpense["user_id"] = auth()->user()->id;

		$travelExpense = array_merge($travelExpense, $totals);
		try {
			$TravelExpenseDB = TravelExpense::create($travelExpense);
			foreach ($hotels as $hotel) {
				$hotel["travel_expense_id"] = $TravelExpenseDB->id;
				TravelExpenseHotel::create($hotel);
			}
			foreach ($taxis as $taxi) {
				$taxi["travel_expense_id"] = $TravelExpenseDB->id;
				TravelExpenseTaxiCity::create($taxi);
			}
			foreach ($feedings as $feeding) {
				$feeding["travel_expense_id"] = $TravelExpenseDB->id;
				TravelExpenseFeeding::create($feeding);
			}
			foreach ($transports as $transport) {
				$transport["travel_expense_id"] = $TravelExpenseDB->id;
				TravelExpenseTransport::create($transport);
			}

			return $this->success("guardado con éxito");
		} catch (\Throwable $th) {
			return $this->errorResponse($th->getMessage(), 500);
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
		return $this->success(
			TravelExpense::with("destiny")
				->with("origin")
				->with("user")
				->with("person")
				->with("hotels")
				->with("transports")
				->with("feedings")
				->with([
					"expenseTaxiCities" => function ($q) {
						$q->select("*")
							->with("taxiCity")
							->with("taxiCity.city")
							->with("taxiCity.taxi");
					},
					/*	"expenseTaxiCities.taxiCity" =>
			function ($q) {
				$q->select("*");
			},*/
				])
				->where("id", $id)
				->first()
		);
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
