<?php

namespace App\Http\Controllers;

use App\Models\AttentionCall;
use App\Models\Memorandum;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemorandumController extends Controller
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
            Memorandum::all()
        );
    }

    public function getMemorandum(){
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize',$data) ? $data['pageSize'] : 5;
        $memorandum = DB::table('people as p')
        ->select(
            'p.image',
            'p.first_name',
            'p.second_name',
            'p.first_surname',
            'p.second_surname',
            'm.details',
            't.name as memorandumType',
            DB::raw('m.level'),
            DB::raw(' "Memorando" as type'),
            DB::raw(' "" as number_call'),
            'm.created_at'
        )
        ->join('memorandum as m', function($join) {
            $join->on('m.person_id', '=', 'p.id');
        })
        ->join('memorandum_types as t', function($join) {
            $join->on('t.id', '=', 'm.memorandum_type_id');
        });
        $attentionCall = DB::table('people as p')
        ->select(
            'p.image',
            'p.first_name',
            'p.second_name',
            'p.first_surname',
            'p.second_surname',
            DB::raw('a.reason as details'),
            DB::raw('"" as memorandumType'),
            DB::raw(' "" as level'),
            DB::raw('"Llamado de atención" as type'),
            'a.number_call',
            'a.created_at'
        )
        ->join('attention_calls as a', function($join) {
            $join->on('a.person_id', '=', 'p.id');
        })
        ->orderBy('a.created_at', 'desc')
        ->union($memorandum)
        ->paginate($pageSize, ['*'],'page', $page);
        return $this->success(
            $attentionCall
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
            Memorandum::create( $request->all() );
            return $this->success('Creado Con Éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function attentionCall(Request $request)
    {
        try {
            AttentionCall::create([
                'reason' => $request->reason,
                'number_call' => $request->number_call,
                'person_id' => $request->person_id,
                'user_id' => auth()->user()->id
            ]);
            return $this->success('Creado Con Éxito');
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
