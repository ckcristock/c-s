<?php

namespace App\Http\Controllers;

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
        return $this->success(
            DB::table('people as p')
            ->select(
                'p.image',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'm.details',
                't.name as memorandumType',
                'm.created_at'
            )
            ->join('memorandum as m', function($join) {
                $join->on('m.person_id', '=', 'p.id');
            })
            ->join('memorandum_Types as t', function($join) {
                $join->on('t.id', '=', 'm.memorandum_type_id');
            })
            ->orderBy('m.created_at', 'desc')
            ->paginate($pageSize, ['*'],'page', $page)
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
