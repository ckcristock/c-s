<?php

namespace App\Http\Controllers;

use App\Models\Marcation;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MarcationController extends Controller
{

    use ApiResponser;

    public function paginate(Request $request)
    {
        return $this->success(
            Marcation::with('person')
            ->when($request->date, function ($q, $fill) {
                //dd(Carbon::parse($fill));
                $q->whereDate('created_at', Carbon::parse($fill));
            })
            ->when($request->person_id, function ($q, $fill) {
                $q->where('person_id', $fill);
            })
            ->where('type', 'error')
            ->orderByDesc('created_at')
            ->paginate(
                request()->get('pageSize', 10),
                ['*'],
                'page',
                request()->get('page', 1)
            )
        );
    }

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
        //
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
