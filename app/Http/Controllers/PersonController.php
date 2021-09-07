<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Models\FixedTurn;
use App\Models\Person;
use App\Models\User;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PersonController extends Controller
{
    public $ocpApimSubscriptionKey = 'df2f7a1cb9a14c66b11a7a2253999da5';
    public $azure_grupo = 'personalnuevo';
    public $uriBase = 'https://facemaqymon2021.cognitiveservices.azure.com/face/v1.0';
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Person::all(['id as value', DB::raw('CONCAT_WS(" ",first_name,first_surname) as text ')]));
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
                ->when($data['dependencies'], function ($q, $fill) {
                    $q->whereIn('d.id', $fill);
                })

                ->when($data['status'], function ($q, $fill) {
                    $q->whereIn('p.status', $fill);
                })

                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }

    public function getAll(Request $request)
    {
        # code...
        $data = $request->all();
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
                    'p.id as value',
                    DB::raw('CONCAT_WS(" ",first_name,first_surname) as text '),
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
                ->where('p.status', 'Activo')
                ->when($data['dependencies'], function ($q, $fill) {
                    $q->where('d.id', $fill);
                })
                ->get()
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
                ->first()
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
                    'p.address',
                    'p.cell_phone',
                    'p.first_name',
                    'p.first_surname',
                    'p.id',
                    'p.image',
                    'p.second_name',
                    'p.second_surname',
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('p.id', '=', 'w.person_id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2 
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }




    public function salary($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'w.date_of_admission',
                    'w.date_end',
                    'w.salary',
                    'wc.name as contract_type',
                    'w.work_contract_type_id',
                    'w.id'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2 
                    join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('work_contract_types as wc', function ($join) {
                    $join->on('wc.id', '=', 'w.work_contract_type_id');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function updateSalaryInfo(Request $request)
    {
        try {
            $salary = WorkContract::find($request->get('id'));
            $salary->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function afiliation($id)
    {
        try {
            return $this->success(
                DB::table('people as p')
                    ->select(
                        'p.eps_id',
                        'e.name as eps_name',
                        'p.compensation_fund_id',
                        'c.name as compensation_fund_name',
                        'p.severance_fund_id',
                        's.name as severance_fund_name',
                        'p.pension_fund_id',
                        'pf.name as pension_fund_name',
                        'a.id as arl_id',
                        'a.name as arl_name'
                    )
                    ->leftJoin('epss as e', function ($join) {
                        $join->on('e.id', '=', 'p.eps_id');
                    })
                    ->leftJoin('arl as a', function ($join) {
                        $join->on('a.id', '=', 'p.arl_id');
                    })
                    ->leftJoin('compensation_funds as c', function ($join) {
                        $join->on('c.id', '=', 'p.compensation_fund_id');
                    })
                    ->leftJoin('severance_funds as s', function ($join) {
                        $join->on('s.id', '=', 'p.severance_fund_id');
                    })
                    ->leftJoin('pension_funds as pf', function ($join) {
                        $join->on('pf.id', '=', 'p.pension_fund_id');
                    })
                    ->where('p.id', '=', $id)
                    ->first()
                /* ->get() */
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function updateAfiliation(Request $request, $id)
    {
        try {
            $afiliation = Person::find($id);
            $afiliation->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function fixed_turn()
    {
        try {
            return $this->success(
                FixedTurn::all(['id as value', 'name as text'])
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function epss()
    {
        try {
            return $this->success(
                EPS::all(['name as text', 'id as value'])
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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


    public function updateBasicData(Request $request, $id)
    {
        try {
            $person = Person::find($id);
            $person->update($request->all());
            return response()->json($person);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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

            $image = $personData['image'];  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . 'jpg';
            /*   return response(base64_decode($image)); */
            $image = base64_decode($image);




            /*   $img = $image->store('funcionarios', 's3', 'public'); */
            /*   $fully =  Storage::disk('s3')->url($image); */
            /* Storage::put(
                'funcionarios',
                file_get_contents($image)->getRealPath()
            ); */
            /*  $upload = Storage::disk('s3')->put('funcionarios/tes.jpg' , $image, 'public');
            $fully =  Storage::disk('s3')->url($upload);
            return response($upload);
 */
            $file_path = 'people/' . Str::random(30) . time() . '.png';
            Storage::disk('public')->put($file_path, $image, 'public');

            $fully = Storage::disk('public')->url($file_path);
            //return response('http://backend.sigmaqmo.com/storage/app/public/'.$file_path); 

            $personData['image'] = 'https://backend.sigmaqmo.com/storage/app/public/' . $file_path;
            $personData['personId'] = null;
            $personData = $this->asure($personData['image'], $personData);
            return response($personData);

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

    public function asure($fully, $atributos)
    {
        if ($fully != '') {

            $atributos['image'] = $fully;
            /** SI LA PERSONA NO TIENE PERSON ID SE CREA EL REGISTRO EN MICROSOFT */
            if ($atributos["personId"] == "0" || $atributos["personId"] == "null"  || $atributos["personId"] == null) {
                /*$request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons');
                $url = $request->getUrl();
                $headers = array(
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                );
                $request->setHeader($headers);
                $parameters = array();
                $body = array(
                    "name" => $atributos["first_name"] . " " . $atributos["first_surname"],
                    "userData" => $atributos["identifier"]
                );
                $url->setQueryVariables($parameters);
                $request->setMethod(\HTTP_Request2::METHOD_POST);
                $request->setBody(json_encode($body));*/
                try {
                    /****** */
                    $parameters = array();
                    $response = Http::accept('application/json')->withHeaders([
                        'Content-Type' => 'application/json',
                        'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey
                    ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons' . http_build_query($parameters), [
                        "name" => $atributos["first_name"] . " " . $atributos["first_surname"],
                        "userData" => $atributos["identifier"]
                    ]);
                    $res = $response->json();
                    /* return ($res); */
                    /****** */

                    /* 
                    $response = $request->send();
                    $resp = $response->getBody();
                    $resp = json_decode($resp); */
                    /* */
                    $atributos["personId"] = $res->personId;
                } catch (HttpException $ex) {
                    echo "error: " . $ex;
                }
            }
            /** VALIDA SI YA TIENE UN ROSTO LO ELIMINA PARA PODER CREAR EL NUEVO */
            if ($atributos["persistedFaceId"] != "0") {
                /* $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces/' . $atributos["persistedFaceId"]);
                $url = $request->getUrl();
                $headers = array(
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                );
                $request->setHeader($headers);
                $parameters = array();
                $url->setQueryVariables($parameters);
                $request->setMethod(\HTTP_Request2::METHOD_DELETE);
                $request->setBody("");
                try {
                    $response = $request->send();
                } catch (HttpException $ex) {
                    echo $ex;
                }*/

                $parameters = array();
                $response = Http::accept('application/json')->withHeaders([
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey
                ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces/' . $atributos["persistedFaceId"] . http_build_query($parameters), [
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                ]);
                $res = $response->json();
            }



            // CREA LOS PUNTOS FACIALES PROPIOS DEL FUNCIONARIO
            $ruta_guardada = $fully;
            /*$request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces');
            $url = $request->getUrl();

            $headers = array(
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );

            $request->setHeader($headers);
            $parameters = array(
                "detectionModel" => "detection_02"
            );
            $body = array(
                "url" => $ruta_guardada
            );
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody(json_encode($body));
*/
            try {
                $parameters = array(
                    "detectionModel" => "detection_02"
                );
                $response = Http::accept('application/json')->withHeaders([
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
                ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/persons/' . $atributos["personId"] . '/persistedFaces' . http_build_query($parameters), [
                    "url" => $ruta_guardada
                ]);
                $resp = $response->json();
                return ($res);

               

                if (isset($resp->persistedFaceId) && $resp->persistedFaceId != '') {
                    $persistedFaceId = $resp->persistedFaceId;
                    $atributos["persistedFaceId"] = $persistedFaceId;
                } else {
                    //  if (Storage::disk('s3')->exists($img)) {
                    //  Storage::disk('s3')->delete($img);
                    //} 
                    if ($resp->error->code == 'InvalidImage') {
                        return response()->json(['message' => 'No se ha encontrado un rostro en la imagen, revise e intente nuevamente'], 400);
                    }
                    return response()->json(['message' => 'Ha ocurrido un error inisperado'], 400);
                }
            } catch (HttpException $ex) {
                echo $ex;
            }
            /*
            // ENTRENA EL GRUPO PARA QUE IDENTIICQUE EL ROSTRO 
            $request = new \Http_Request2($this->uriBase . '/persongroups/' . $this->azure_grupo . '/train');
            $url = $request->getUrl();

            $headers = array(
                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            );
            $request->setHeader($headers);
            $parameters = array();
            $url->setQueryVariables($parameters);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setBody("");
            try {
                $response = $request->send();
                echo $response->getBody();
            } catch (HttpException $ex) {
                echo $ex;
            }*/
        }
        return $atributos;
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
