<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function PHPSTORM_META\map;

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
        return $this->success(Person::all(['id as value', DB::raw( 'CONCAT_WS(" ",first_name,first_surname) as text ')]));
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
                ->join('companies as c', 'c.id', '=', 'w.company_id')
                ->join('positions as pos', 'pos.id', '=', 'w.position_id')
                ->join('dependencies as d', 'd.id', '=', 'pos.dependency_id')
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

    public function basicData($id)
    {
        return $this->success(
            DB::table('people as p')
            ->select(
                'p.first_name',
                'p.first_surname',
                'p.id',
                'p.image',
                'p.second_name',
                'p.second_surname',
                'w.salary',
                'w.id as work_contract_id',
                'p.signature',
                'p.title'
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2 
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->where('p.id', '=', $id)
            ->get()
        );
    }

    public function basicDataForm($id)
    {
        return $this->success(
            DB::table('people as p')
            ->select(
                'p.first_name',
                'p.first_surname',
                'p.second_name',
                'p.second_surname',
                'p.identifier',
                'p.image',
                'p.email',
                'p.degree',
                'p.date_of_birth',
                'p.gener',
                'p.marital_status',
                'p.direction',
                'p.cell_phone'
            )
            ->where('p.id', '=', $id)
            ->get()
        );
    }

    public function enterpriseData($id)
    {
        return $this->success(
            DB::table('people as p')
            ->select(
                'p.id',
                'w.turn_type',
                'posi.name as position_name',
                'd.name as depencency_name',
                'gr.name as group_name',
                'f.name as fixed_turn_name',
                'c.name as company_name',
                'w.turn_type'
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2 
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('positions as posi', function ($join) {
                $join->on('posi.id', '=', 'p.id');
            })
            ->join('dependencies as d', function ($join) {
                $join->on('d.id', '=', 'p.person_id');
            })
            ->join('groups as gr', function ($join) {
                $join->on('gr.id', '=', 'posi.id');
            })
            ->join('fixed_turns as f', function ($join) {
                $join->on('f.id', '=', 'w.fixed_turn_id');
            })
            ->join('companies as c', function ($join) {
                $join->on('c.d', '=', 'p.company_id');
            })
            ->where('p.id', '=', $id)
            ->get()
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
        $person = Person::find($person);
        return response()->json($person, 200);
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
