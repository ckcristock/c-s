<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
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

    /**
     * Display a listing of the resource paginated.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginate()
    {
        $data = json_decode( Request()->get('data'), true);
        $page = $data['page'] ? $data['page'] : 1;
        $pageSize = $data['pageSize'] ? $data['pageSize'] : 10;   

        return $this->success(
            DB::table('people')
                ->select(
                    'people.id',
                    'people.identifier',
                    'people.image',
                    'people.status',
                    'full_name',
                    'first_surname',
                    'first_name'
                )
                ->when( $data ['name'], function ($q, $fill) {
                    $q->where('people.identifier', 'like', '%' . $fill . '%')
                    ->orWhere(DB::raw('concat(first_name," ",first_surname)') , 'LIKE' , '%'.$fill.'%');
                })
                ->when( $data ['dependencies'], function ($q, $fill) {
                    
                    $q->whereIn('people.dependency_id', $fill);
                })
                ->when( $data ['status'], function ($q, $fill) {
                    $q->whereIn('people.status', $fill);
                })
                ->paginate($pageSize, ['*'], 'page')
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
        //
        return $this->success($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }
}
