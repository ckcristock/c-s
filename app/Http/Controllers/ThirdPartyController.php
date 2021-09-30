<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\ThirdPartyPerson;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThirdPartyController extends Controller
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
            ThirdParty::with('municipality')
            ->when( Request()->get('nit') , function($q, $fill)
            {
                $q->where('nit','like','%'.$fill.'%');
            })
            ->when( Request()->get('name') , function($q, $fill)
            {
                $q->where(DB::raw('concat(first_name," ",first_surname)'),'like','%'.$fill.'%');
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
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except(["person"]);
        $people = request()->get('person');
        $file = $request->file('rut')->store('public');
        try {
            $thirdParty =  ThirdParty::create($data);
            foreach ($people as $person) {
                $person["third_party_id"] = $thirdParty->id;
                ThirdPartyPerson::create($person);
            }
            return $this->success('Guardado con éxito');
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
        return $this->success(
            ThirdParty::with('thirdPartyPerson')
            ->find($id)
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
        $data = $request->except(["person"]);
        $people = request()->get('person');
        try {
            $thirdParty = ThirdParty::find($id)
            ->update($data);
            foreach ($people as $person) { 
                $thirdPerson = ThirdPartyPerson::find($person["id"]);
                $thirdPerson->update($person);
            }
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
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
