<?php

namespace App\Http\Controllers;

use App\Models\Lunch;
use App\Models\LunchPerson;
use App\Models\Person;
use App\Traits\ApiResponser;
use Faker\Calculator\Luhn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LunchControlller extends Controller
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
            Lunch::with('person')->get()
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
            $lunch = Lunch::create([
                'person_id' => $request->person_id,
                'value' => $request->value
            ]);
            $lunchPerson = LunchPerson::create([
                'user_id' => auth()->user()->id,
                'lunch_id' => $request->id
            ]);
            return $this->success([
                'lunch' => $lunch,
                'lunchPerson' => $lunchPerson
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function activateOrInactivate( Request $request )
    {
        try {
            $state = Lunch::find($request->get('id'));
            $state->update($request->all());
            return $this->success('Actualizado con éxito');
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
        return $this->success(
            Lunch::with('person')
            ->with('lunchPerson')
            ->where('id', '=', $id)
            ->get()
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
