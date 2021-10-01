<?php

namespace App\Http\Controllers;

use App\Models\Disciplinary_process;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Disciplinary_processController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize',$data) ? $data['pageSize'] : 5;
        return $this->success(
            DB::table('people as p')
            ->select(
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'd.process_description',
                'd.status',
                'd.date_of_admission',
                'd.date_end',
                'd.id'
            )
            ->when( Request()->get('person') , function($q, $fill)
            {
                $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
            })
            ->join('disciplinary_process as d', function($join) {
                $join->on('d.person_id', '=', 'p.id');
            })
            ->when( Request()->get('status'), function($q, $fill) {
                $q->where('d.status', 'like', '%' . $fill . '%');
            })
            ->when( Request()->get('code'), function($q, $fill) {
                $q->where('d.id', 'like', '%' . $fill . '%');
            })
            ->paginate($pageSize, ['*'],'page', $page)
        );
    }

    /* public function getHistory(){
       
    } */

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
            Disciplinary_process::create($request->all());
            return $this->success('Creado con éxito');
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
            DB::table('memorandums as m')
            ->select(   
                'm.created_at as created_at_memorandum',
                't.name as memorandumType',
                'p.first_name',
                'm.details'
            )
            ->join('people as p', function($join) {
                $join->on('p.id', '=', 'm.person_id');
            })
            ->join('memorandum_types as t', function($join) {
                $join->on('t.id', '=', 'm.memorandum_type_id');
            })
            ->where('p.id', '=', $id)
            ->get()
        );
    }

    public function process($id)
    {
        return $this->success(
            DB::table('disciplinary_process as d')
            ->select(
                'd.process_description',
                'd.created_at as created_at_process'
            )
            ->join('people as p', function($join) {
                $join->on('p.id', '=', 'd.person_id');
            })
            ->where('p.id', '=', $id)
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
