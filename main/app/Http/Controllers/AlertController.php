<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    //
    use ApiResponser;

    public function index(Request $req)
    {
        # code...
        $alerts = Alert::where('user_id', $req->user_id)->with('transmitter')->orderByDesc('created_at');
        $temp = $alerts->get();
        $data = $alerts->limit(99)->get();
        $count = 0;
        foreach($temp as $dat) {
            if ($dat->read_boolean == 0){
                $count++;
            }
        }
        return $this->success(
            $data, $count
        );
    }

    public function read(Request $request){
        $alert = Alert::where('id', $request->id)->first();
        $alert->update(['read_boolean' => 1]);
        
        $alerts = Alert::where('user_id', $request->user_id)->with('transmitter')->orderByDesc('created_at');
        $temp = $alerts->get();
        $data = $alerts->limit(99)->get();
        $count = 0;
        foreach($temp as $dat) {
            if ($dat->read_boolean == 0){
                $count++;
            }
        }
        return $this->success(
            $data, $count
        );
    }

    public function paginate(Request $req)
    {
        $data = DB::table('alerts as a')
            ->join('people as pc', 'pc.id', '=', 'a.user_id')
            ->select(
                'a.type',
                'a.description',
                'a.destination_id',
                'a.created_at',
                'a.url',
                'a.icon',
                'pc.first_name',
                'pc.first_surname',
            )
            ->when($req->get('person_id'), function ($q, $fill) {
                $q->where('a.user_id', $fill);
            })
            ->orderBy('a.id', 'Desc')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
        return $this->success($data);
    }

    public function store(Request $request)
    {
        $person_id = request()->get("person_id");
        $type = request()->get("type");
        $user_id = request()->get("user_id");
        $description = request()->get("description");
        $group_id = request()->get("group_id");
        $dependency_id = request()->get("dependency_id");
        if ($group_id != "Todas") {
            if ($dependency_id != "Todas") {
                if ($user_id != "Todos") {
                    Alert::create($request->all());
                    return $this->success('Agregada corrrectamente');
                } else {
                    $dependencie = DB::table("dependencies")
                        ->where("id", "=", $dependency_id)
                        ->first();
                    $positions = DB::table("positions")
                        ->where("dependency_id", "=", $dependencie->id)
                        ->get();
                    foreach ($positions as $position) {
                        $people = DB::table("work_contracts")
                            ->where("position_id", "=", $position->id)
                            ->get();
                        foreach ($people as $person) {
                            $user_id = $person->person_id;
                            Alert::create(
                                [
                                    'person_id' => $person_id,
                                    'user_id' => $user_id,
                                    'type' => $type,
                                    'description' => $description
                                ],
                            );
                        }
                    }
                    return $this->success('Agregado correctamente');
                }
            } else {
                $group = DB::table("groups")
                    ->where("id", "=", $group_id)
                    ->first();
                $dependencies = DB::table("dependencies")
                    ->where("group_id", "=", $group->id)
                    ->get();
                foreach ($dependencies as $dependencie) {
                    $positions = DB::table("positions")
                        ->where("dependency_id", "=", $dependencie->id)
                        ->get();
                    foreach ($positions as $position) {
                        $people = DB::table("work_contracts")
                            ->where("position_id", "=", $position->id)
                            ->get();
                        foreach ($people as $person) {
                            $user_id = $person->person_id;
                            Alert::create(
                                [
                                    'person_id' => $person_id,
                                    'user_id' => $user_id,
                                    'type' => $type,
                                    'description' => $description
                                ],
                            );
                        }
                    }
                }
            }
        } else {
            $people = DB::table("work_contracts")
                ->get();
            foreach ($people as $person) {
                $user_id = $person->person_id;
                Alert::create(
                    [
                        'person_id' => $person_id,
                        'user_id' => $user_id,
                        'type' => $type,
                        'description' => $description
                    ],
                );
            }
        }
    }
}
