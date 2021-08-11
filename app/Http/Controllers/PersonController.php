<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $data = json_decode(Request()->get('data'), true);
        $page = $data['page'] ? $data['page'] : 1;
        $pageSize = $data['pageSize'] ? $data['pageSize'] : 10;


        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id',
                    'p.identifier',
                    'p.image',
                    'p.status',
                    'p.full_name',
                    'p.first_surname',
                    'p.first_name',
                    'pos.name as position',
                    'd.name as dependency',
                    'c.name as company',
                    DB::raw('w.id AS work_contract_id')
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('p.id', '=', 'w.person_id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2 
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('positions as pos', 'pos.id', '=', 'w.position_id')
                ->join('dependencies as d', 'd.id', '=', 'pos.dependency_id')
                ->join('companies as c', 'c.id', '=', 'd.company_id')
                ->when($data['name'], function ($q, $fill) {
                    $q->where('p.identifier', 'like', '%' . $fill . '%')
                        ->orWhere(DB::raw('concat(p.first_name," ",p.first_surname)'), 'LIKE', '%' . $fill . '%');
                })

                ->when( $data ['dependencies'], function ($q, $fill) {
                    $q->whereIn('d.id', $fill);
                })

                ->when($data['status'], function ($q, $fill) {
                    $q->whereIn('p.status', $fill);
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
        try {
            $personData = $request->get('person');
            $person = Person::create($personData);
            $contractData = $personData['workContract'];
            $contractData['person_id'] = $person->id;
            WorkContract::create($contractData);

            User::create([
                'person_id' => $person->id,
                'usuario' => $person->identifier,
                'password' => Hash::make($person->identifier),
                'change_password' => 1,
            ]);
            return $this->success(['id' => $person->id]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
