<?php

namespace App\Http\Controllers;

use App\Models\AttentionCall;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionCallController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            
    }

    public function callAlert($id)
    {
        $call = DB::table('attention_calls')
            ->select(DB::raw('person_id, count(*) as cantidad'))
            ->whereRaw('month(created_at) = MONTH(CURRENT_DATE())')
            ->groupBy('person_id')
            ->havingRaw('COUNT(*) > 1')
            ->where('person_id', $id)
            ->first();
        return $call;
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
            AttentionCall::create([
                'reason' => $request->get('reason'),
                'number_call' => $request->get('number_call'),
                'person_id' => $request->get('person_id'),
                'user_id' => auth()->user()->id
            ]);
            return $this->success('Creado Con éxito');
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
