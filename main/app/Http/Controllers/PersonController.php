<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Models\FixedTurn;
use App\Models\Person;
use App\Models\User;
use App\Models\WorkContract;
use App\Services\CognitiveService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Intervention\Image\Facades\Image;

class PersonController extends Controller
{
	public $ocpApimSubscriptionKey = "df2f7a1cb9a14c66b11a7a2253999da5";
	public $azure_grupo = "personalnuevo";
	public $uriBase = "https://facemaqymon2021.cognitiveservices.azure.com/face/v1.0";
	use ApiResponser;

    // ! Al liquidar un funcionario tendríamos que validar si este es responsable de algo en algún lado
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		return $this->success(
			Person::
            where('status', 'Activo')
            ->when($request->dependency_id, function ($q, $fill) {
                $q->whereHas('work_contract', function ($q2) use ($fill) {
                    $q2->whereHas('position', function ($q3) use ($fill) {
                        $q3->where('dependency_id', '=', $fill);
                    });
                });
            })
            ->get([
				"id as value",
				DB::raw('CONCAT_WS(" ",first_name, second_name, first_surname, second_surname) as text '),
			])
		);
	}

	public function peopleSelects()
	{
		return $this->success(
			Person::select('id as value', DB::raw('CONCAT_WS(" ", first_name, first_surname) as text '))
				->when(request()->get('name'), function ($q, $fill) {
					$q->where(DB::raw('concat(first_name," ",first_surname)'), 'like', '%' . $fill . '%');
				})
				->limit(100)
				->get()
		);
	}

    public function funcionarioPunto(){
        return $this->success(
            DB::table("Funcionario_Punto", "FP")
            ->select("PD.Id_Punto_Dispensacion","PD.Nombre")
            ->join("Punto_Dispensacion as PD", function($join){
                $join->on("FP.Id_Punto_Dispensacion", "PD.Id_Punto_Dispensacion");
            })
            ->when(request()->id, function ($q, $fill) {
                $q->where("FP.Identificacion_Funcionario", $fill);
            })->get()
        );
    }

	/**
	 * Display a listing of the resource paginated.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function indexPaginate(Request $request)
	{
		return $this->success(
			Person::with('work_contract')
				->when($request->name, function ($q, $fill) {
					$q->where("identifier", "like", "%" . $fill . "%")
						->orWhere(DB::raw('CONCAT_WS(" ", first_name, first_surname, second_surname)'), "LIKE", "%" . $fill . "%")
						->orWhere(DB::raw('CONCAT_WS(" ", second_name, first_surname, second_surname)'), "LIKE", "%" . $fill . "%")
						->orWhere(DB::raw('CONCAT_WS(" ", first_surname, second_surname)'), "LIKE", "%" . $fill . "%")
						->orWhere(DB::raw('CONCAT_WS(" ", first_name, second_name, first_surname, second_surname)'), "LIKE", "%" . $fill . "%");
				})
				->when($request->dependency_id, function ($q, $fill) {
					$q->whereHas('work_contract', function ($q2) use ($fill) {
						$q2->whereHas('position', function ($q3) use ($fill) {
							$q3->where('dependency_id', '=', $fill);
						});
					});
				})

				->when($request->status, function ($q, $fill) {
					$q->where("status", $fill);
				})
				->orderBy('first_name', 'asc')
				->paginate(Request()->get('pageSize', 12), ['*'], 'page', Request()->get('page', 1))
		);

		/* $data = json_decode(Request()->get("data"), true);
		$page = $data["page"] ? $data["page"] : 1;
		$pageSize = $data["pageSize"] ? $data["pageSize"] : 12;

		return $this->success(
			DB::table("people as p")
				->select(
					"p.id",
					"p.identifier",
					"p.image",
					"p.status",
					"p.full_name",
					"p.first_surname",
					"p.first_name",
					"pos.name as position",
					"d.name as dependency",
					"c.name as company",
					DB::raw("w.id AS work_contract_id")
				)
				->join("work_contracts as w", function ($join) {
					$join->on(
						"p.id",
						"=",
						"w.person_id"
					)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
				})
				->join("companies as c", "c.id", "=", "w.company_id")
				->join("positions as pos", "pos.id", "=", "w.position_id")
				->join("dependencies as d", "d.id", "=", "pos.dependency_id")
				->when($data["name"], function ($q, $fill) {
					$q->where(
						"p.identifier",
						"like",
						"%" . $fill . "%"
					)->orWhere(
						DB::raw('concat(p.first_name," ",p.first_surname)'),
						"LIKE",
						"%" . $fill . "%"
					);
				})
				->when($data["dependencies"], function ($q, $fill) {
					$q->whereIn("d.id", $fill);
				})

				->when($data["status"], function ($q, $fill) {
					$q->whereIn("p.status", $fill);
				})
				->orderBy('p.first_name', 'asc')
				->paginate($pageSize, ["*"], "page", $page)
		); */
	}

    /***
     * Función que me devuelve el id, identificador,
     * y nombre+apellido de personas (people)
     */
    public function peoplesWithDni(Request $request)
    {
		//dd(request()->get('dni'));
        return $this->success(
			Person::select(
				"id as value",
                "identifier",
				DB::raw('CONCAT_WS(" ",first_name, second_name, first_surname, second_surname) as text '))
				->when(request('search'), function ($q, $fill) {
					$q->where(DB::raw('identifier'), 'like', '%' .$fill. '%')
					  ->orWhere(DB::raw('CONCAT_WS(" ", first_name, second_name, first_surname, second_surname)'), 'like', '%'.$fill.'%');
				})
                ->where('status', 'Activo')
				->get()
		);
    }

	public function validarCedula($documento)
	{
		$user= '';
		$person = DB::table("people")
			->where('identifier', $documento)
			->exists();
		if ($person) {
			$user =  DB::table('people')->where('identifier', $documento)->first();
		}
		return $this->success($person, $user);
	}

	public function getAll(Request $request)
	{
		# code...
		$data = $request->all();
		return $this->success(
			DB::table("people as p")
				->select(
					"p.id",
					"p.identifier",
					"p.image",
					"p.status",
					"p.full_name",
					"p.first_surname",
					"p.first_name",
					"pos.name as position",
					"d.name as dependency",
					"p.id as value",
					"p.passport_number",
					"p.visa",
					DB::raw('CONCAT_WS(" ",first_name, second_name, first_surname, second_surname) as text '),
					"c.name as company",
					DB::raw("w.id AS work_contract_id"),
					DB::raw("'Funcionario' AS type")
				)
				->join("work_contracts as w", function ($join) {
					$join->on(
						"p.id",
						"=",
						"w.person_id"
					)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
				})
				->join("companies as c", "c.id", "=", "w.company_id")
				->join("positions as pos", "pos.id", "=", "w.position_id")
				->join("dependencies as d", "d.id", "=", "pos.dependency_id")
				->where("p.status", "Activo")
				->when($request->get('dependencies'), function ($q, $fill) {
					$q->where("d.id", $fill);
				})
				->get()
		);
	}

	public function basicData($id)
	{
		return $this->success(
			DB::table("people as p")
				->select(
					"p.first_name",
					"p.first_surname",
					"p.id",
                    "p.identifier",
					"p.image",
					"p.second_name",
					"p.second_surname",
					"w.salary",
					"w.id as work_contract_id",
					"p.signature",
					"p.title",
					"p.status"
				)
				->join("work_contracts as w", function ($join) {
					$join->on(
						"p.id",
						"=",
						"w.person_id"
					)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
				})
				->where("p.id", "=", $id)
				->first()
		);
	}

	public function basicDataForm($id)
	{
		return $this->success(
			DB::table("people as p")
				->select(
					"p.first_name",
					"p.first_surname",
					"p.second_name",
					"p.second_surname",
					"p.identifier",
					"p.image",
					"p.email",
					"p.degree",
					"p.birth_date",
					"p.gener",
					"p.marital_status",
					"p.address",
					"p.cell_phone",
					"p.first_name",
					"p.first_surname",
					"p.id",
					"p.image",
					"p.second_name",
					"p.second_surname",
					"p.status",
					"p.visa",
					"p.passport_number",
					"p.title",
                    "p.address"

				)
				->join("work_contracts as w", function ($join) {
					$join->on(
						"p.id",
						"=",
						"w.person_id"
					)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
				})
				->where("p.id", "=", $id)
				->first()
		);
	}

	public function salary($id)
	{
		return $this->success(
			DB::table("people as p")
				->select(
					"w.date_of_admission",
					"w.date_end",
					"w.salary",
					"wc.name as contract_type",
					"ct.name as contract_term",
					"ct.conclude",
					"w.work_contract_type_id",
					"w.contract_term_id",
					"w.id"
				)
				->join("work_contracts as w", function ($join) {
					$join->on(
						"w.person_id",
						"=",
						"p.id"
					)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                    join people as u2 on u2.id = a2.person_id group by u2.id)');
				})
				->join("work_contract_types as wc", function ($join) {
					$join->on("wc.id", "=", "w.work_contract_type_id");
				})
				->leftJoin("contract_terms as ct", function ($join) {
					$join->on("ct.id", "=", "w.contract_term_id");
				})
				->where("p.id", "=", $id)
				->first()
		);
	}

	public function salaryHistory($id)
	{
		return $this->success(
			WorkContract::where('person_id', $id)
				->where('liquidated', 1)
				->with('work_contract_type', 'position')
				->orderBy('date_end')
				->get()
		);
	}

	public function updateSalaryInfo(Request $request)
	{
		try {
			$salary = WorkContract::find($request->get("id"));
			$salary->update($request->all());
			return response()->json([
				"message" => "Se ha actualizado con éxito",
			]);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}

	public function afiliation($id)
	{
		try {
			return $this->success(
				DB::table("people as p")
					->select(
						"p.eps_id",
						"e.name as eps_name",
						"p.compensation_fund_id",
						"c.name as compensation_fund_name",
						"p.severance_fund_id",
						"s.name as severance_fund_name",
						"p.pension_fund_id",
						"pf.name as pension_fund_name",
						"a.id as arl_id",
						"a.name as arl_name"
					)
					->leftJoin("epss as e", function ($join) {
						$join->on("e.id", "=", "p.eps_id");
					})
					->leftJoin("arl as a", function ($join) {
						$join->on("a.id", "=", "p.arl_id");
					})
					->leftJoin("compensation_funds as c", function ($join) {
						$join->on("c.id", "=", "p.compensation_fund_id");
					})
					->leftJoin("severance_funds as s", function ($join) {
						$join->on("s.id", "=", "p.severance_fund_id");
					})
					->leftJoin("pension_funds as pf", function ($join) {
						$join->on("pf.id", "=", "p.pension_fund_id");
					})
					->where("p.id", "=", $id)
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
			return response()->json([
				"message" => "Se ha actualizado con éxito",
			]);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}

	public function fixed_turn()
	{
		try {
			return $this->success(
				FixedTurn::all(["id as value", "name as text"])
			);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}

	public function liquidateOrActivate(Request $request, $id)
	{
		try {
			$person = Person::find($id);
			$person->update([
				'status' => $request->status,
			]);
			return $this->success('Liquidado con éxito');
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}

	public function epss()
	{
		try {
			return $this->success(EPS::all(["name as text", "id as value"]));
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

			$personData = $request->all();
			$cognitive = new CognitiveService();
			if (!$person->personId) {
				//return '0';
				$person->personId = $cognitive->createPerson($person);
				$person->save();
				$cognitive->deleteFace($person);
			}
			if ($request->image != $person->image) {
                $personData["image"] = URL::to('/') . '/api/image?path=' . saveBase64($personData["image"], 'people/');
                $person->update($personData);
                $cognitive->deleteFace($person);
                $person->persistedFaceId = $cognitive->createFacePoints($person); //esta línea está dando un error
                $person->save();
                $cognitive->train();
            } else {
                $person->update($personData);
            }

			return response()->json($person);
		} catch (\Throwable $th) {
			return $this->error($th->getMessage(), 500);
		}
	}


    public function updateFilePermission(Request $request)
    {
        $person = Person::where('id', $request->id)->first();
        $person->update(['folder_id' => $request->folder_id]);
    }

    public function getFilePermission($id)
    {
        $person = Person::where('id', $id)->pluck('folder_id')->first();
        return $this->success($person);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$per = [];
		try {

			$personData = $request->get("person");

			$personData["image"] = URL::to('/') . '/api/image?path=' . saveBase64($personData["image"], realpath('../../../public/app/public/people/'));

			$personData["personId"] = null;

			$per = $person = Person::create($personData);
			$contractData = $personData["workContract"];
			$contractData["person_id"] = $person->id;
			WorkContract::create($contractData);
			User::create([
				"person_id" => $person->id,
				"usuario" => $person->identifier,
				"password" => Hash::make($person->identifier),
				"change_password" => 1,
			]);
			//crear personID
			$cognitive = new CognitiveService();
			$person->personId = $cognitive->createPerson($person);


			if ($personData["image"]) {
				$cognitive->deleteFace($person);
				$person->persistedFaceId = $cognitive->createFacePoints(
					$person
				);
			}
			$person->save();
			$cognitive->train();

			return $this->success(["id" => $person->id, 'faceCreated' => true]);
		} catch (\Throwable $th) {
			if ($per) {
				return $this->success(["id" => $person->id, 'faceCreated' => false]);
			}
			return $this->error($th->getMessage(), 500);
		}
	}

	public function train()
	{
		/*try {
            $response = Http::accept('application/json')->withHeaders([

                'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey,
            ])->post($this->uriBase . '/persongroups/' . $this->azure_grupo . '/train', [

            ]);
            $resp = $response->json();
            return response()->json($resp);
        } catch (HttpException $ex) {
            echo $ex;
        }
        */
		$people = Person::whereNotNull("image")
			//->whereNull('personId')
			//->whereNull('persistedFaceId')
			->orderBy("id", "Desc")
			//->limit(1)
			->get();
		//dd($people);
		$x = 0;
		foreach ($people as $person) {
			dd((array) $person);
			$x++;
			if ($x == 7) {
				sleep(15);
				$x = 0;
			}
			// $atributos['image'] = $fully;
			/** SI LA PERSONA NO TIENE PERSON ID SE CREA EL REGISTRO EN MICROSOFT */
			if (
				$person->personId == "0" ||
				$person->personId == "null" ||
				$person->personId == null
			) {
				try {
					/****** */
					$parameters = [];
					$response = Http::accept("application/json")
						->withHeaders([
							"Content-Type" => "application/json",
							"Ocp-Apim-Subscription-Key" =>
							$this->ocpApimSubscriptionKey,
						])
						->post(
							$this->uriBase .
								"/persongroups/" .
								$this->azure_grupo .
								"/persons" .
								http_build_query($parameters),
							[
								"name" =>
								$person->first_name .
									" " .
									$person->first_surname,
								"userData" => $person->identifier,
							]
						);
					$res = $response->json();
					if (!isset($res["personId"])) {
						return response()->json($res);
					}
					$person->personId = $res["personId"];
				} catch (HttpException $ex) {
					echo "error: " . $ex;
				}
			}

			if ($person->image) {
				//$atributos
				/** VALIDA SI YA TIENE UN ROSTO LO ELIMINA PARA PODER CREAR EL NUEVO */
				if (
					isset($person->persistedFaceId) &&
					$person->persistedFaceId != "0"
				) {
					$parameters = [];
					$response = Http::accept("application/json")
						->withHeaders([
							"Content-Type" => "application/json",
							"Ocp-Apim-Subscription-Key" =>
							$this->ocpApimSubscriptionKey,
						])
						->post(
							$this->uriBase .
								"/persongroups/" .
								$this->azure_grupo .
								"/persons/" .
								$person->personId .
								"/persistedFaces/" .
								$person->persistedFaceId .
								http_build_query($parameters),
							[
								"Ocp-Apim-Subscription-Key" =>
								$this->ocpApimSubscriptionKey,
							]
						);
					$res = $response->json();
				}

				// CREA LOS PUNTOS FACIALES PROPIOS DEL FUNCIONARIO
				$ruta_guardada = $person->image;
				try {
					$parameters = [
						"detectionModel" => "detection_02",
					];
					$response = Http::accept("application/json")
						->withHeaders([
							"Content-Type" => "application/json",
							"Ocp-Apim-Subscription-Key" =>
							$this->ocpApimSubscriptionKey,
						])
						->post(
							$this->uriBase .
								"/persongroups/" .
								$this->azure_grupo .
								"/persons/" .
								$person->personId .
								"/persistedFaces",
							[
								"url" => $ruta_guardada,
								"detectionModel" => "detection_02",
							]
						);
					$resp = $response->json();

					if (
						isset($resp["persistedFaceId"]) &&
						$resp["persistedFaceId"] != ""
					) {
						$persistedFaceId = $resp["persistedFaceId"];
						$person->persistedFaceId = $persistedFaceId;
					} else {
						//  if (Storage::disk('s3')->exists($img)) {
						//  Storage::disk('s3')->delete($img);
						//}
						if ($resp["error"]["code"] == "InvalidImage") {
							return response()->json([$resp, $person], 400);
						}
						return response()->json($resp, 400);
					}
				} catch (HttpException $ex) {
					echo $ex;
				}
			}
			$person->save();
		}

		try {
			$response = Http::accept("application/json")
				->withHeaders([
					"Ocp-Apim-Subscription-Key" =>
					$this->ocpApimSubscriptionKey,
				])
				->post(
					$this->uriBase .
						"/persongroups/" .
						$this->azure_grupo .
						"/train",
					[
						//"url" => $ruta_guardada
					]
				);
			$resp = $response->json();
		} catch (HttpException $ex) {
			echo $ex;
		}
	}

	public function user($id)
	{
		return $this->success(
			User::where('person_id', $id)->first()
		);
	}

	public function blockOrActivateUser(Request $request, $id)
	{
		try {
			User::where('person_id', $id)->update(['state' => $request->state]);
			return $this->success('Actualizado con éxito');
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
