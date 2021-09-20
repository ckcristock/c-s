<?php

namespace App\Http\Controllers;

use App\Models\TravelExpense;
use App\Models\TravelExpenseFeeding;
use App\Models\TravelExpenseHotel;
use App\Models\TravelExpenseTaxi;
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
        $hotels = request()->get('hospedaje');
        $taxis = request()->get('taxi');
        $feedings = request()->get('feeding');
        $transports = request()->get('transporte');
        $travelExpense = request()->get('travel');
        try {
            TravelExpense::create([
                $travelExpense->arrival_date => 'arrival_date',
                $travelExpense->departure_date => 'departure_date',
                $travelExpense->destiny => 'destiny',
                $travelExpense->n_nights => 'n_nights',
                $travelExpense->origin => 'origin',
                $travelExpense->person_id => 'person_id',
                $travelExpense->travel_type => 'travel_type'
            ]);
            foreach ($hotels as $hotel ) {
                $travelHotel = new TravelExpenseHotel();
                $travelHotel->hotel_id = $hotel['hotel_id'];
                $travelHotel->n_night = $hotel['n_night'];
                $travelHotel->who_cancels = $hotel['who_cancels'];
                $travelHotel->breakfast = $hotel['breakfast'];
                $travelHotel->save();
            }
            foreach ($taxis as $taxi) {
                $TravelTaxi = new TravelExpenseTaxi();
                $TravelTaxi->journeys = $taxi['journeys'];
                $TravelTaxi->journey = $taxi['journey'];
                $TravelTaxi->type = $taxi['type'];
                $TravelTaxi->city = $taxi['city'];
                $TravelTaxi->rate = $taxi['rate'];
                $TravelTaxi->total = $taxi['total'];
                $TravelTaxi->save();
            }
            foreach ($feedings as $feeding) {
                $travelFeeding = new TravelExpenseFeeding();
                $travelFeeding->stay = $feeding['stay'];
                $travelFeeding->rate = $feeding['rate'];
                $travelFeeding->total = $feeding['total'];
                $travelFeeding->type = $feeding['type'];
                $travelFeeding->person_type = $feeding['personType'];
                $travelFeeding->breakfast = $feeding['breakfast'];
                $travelFeeding->save();
            }
            foreach ($transports as $transport) {
                $travelTransport = new TravelExpenseTransport();
                $travelTransport->type = $transport['type'];
                $travelTransport->journey = $transport['journey'];
                $travelTransport->company = $transport['company'];
                $travelTransport->ticket_payment = $transport['ticket_payment'];
                $travelTransport->departure_date = $transport['departure_date'];
                $travelTransport->ticket_value = $transport['ticket_value'];
                $travelTransport->save();
            }            
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
