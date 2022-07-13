<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyPerson;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThirdPartyPersonController extends Controller
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
            DB::table('third_party_people as tp')
            ->select(
                'tp.id', 'tp.name', 'tp.observation', 'tp.cell_phone', 'tp.email', 'tp.position', 'tp.n_document', 'tp.third_party_id',
                DB::raw('(CASE WHEN (tp.third_party_id=0) THEN "Sin tercero" ELSE concat(t.first_name," ",t.first_surname) END) as third_party'),
            )
            ->when(request()->get('third'), function($q, $fill)
            {
                $q->where(DB::raw('concat(t.first_name," ",t.first_surname)'), 'like','%'.$fill.'%');
            })
            ->when(request()->get('name'), function($q, $fill)
            {
                $q->where('tp.name', 'like','%'.$fill.'%');
            })
            ->when(request()->get('phone'), function($q, $fill)
            {
                $q->where('tp.cell_phone', 'like','%'.$fill.'%');
            })
            ->when(request()->get('email'), function($q, $fill)
            {
                $q->where('tp.email', 'like','%'.$fill.'%');
            })
            ->when(request()->get('cargo'), function($q, $fill)
            {
                $q->where('tp.position', 'like','%'.$fill.'%');
            })
            ->when(request()->get('observacion'), function($q, $fill)
            {
                $q->where('tp.observation', 'like','%'.$fill.'%');
            })
            ->when(request()->get('documento'), function($q, $fill)
            {
                $q->where('tp.n_document', 'like','%'.$fill.'%');
            })
            ->leftJoin('third_parties as t', 't.id', '=', 'tp.third_party_id')
            ->orderBy('name', 'asc')
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
        $data = $request->all();
        $data["third_party_id"] = 0;
        /* return $this->success($request->all()); */
        try {
            ThirdPartyPerson::create($data);
            return $this->success('Creado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
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
