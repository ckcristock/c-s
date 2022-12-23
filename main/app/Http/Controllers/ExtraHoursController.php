<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Deduction;
use App\Models\ExtraHourReport;
use App\Models\Lunch;
use App\Services\ExtraHoursService;
use App\Services\LateArrivalService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isJson;

class ExtraHoursController extends Controller
{
	use ApiResponser;
	public function __construct(ExtraHoursService $extrasService)
	{
		$this->extrasService = $extrasService;
	}

	//
	public function getDataRotative($fechaInicio, $fechaFin, $tipoTurno)
	{
		$dates = [$fechaInicio, $fechaFin];

		$companies = Company::when(Request()->get('company_id'), function ($q, $fill) {
			$q->where('id', $fill);
		})->get();

		foreach ($companies as $keyG => &$company) {

			$groups = DB::table("groups")
				->when(Request()->get('group_id'), function ($q, $fill) {
					$q->where('id', $fill);
				})->get(["id", "name"]);

			$groupsGlob = [];
			foreach ($groups as $key => &$group) {


				$group->dependencies = DB::table('dependencies')
					->when(Request()->get('dependency_id'), function ($q, $fill) {
						$q->where('id', $fill);
					})
					->where('group_id', $group->id)->get();

				if ($group->dependencies->isEmpty()) {
					unset($groups[$key]);
					continue;
				}

				foreach ($group->dependencies as $key2 =>  &$dependency) {

					$dependency->people =  $this->extrasService->getPeople($dependency->id, $tipoTurno, $company->id, $dates);
					if ($dependency->people->isEmpty()) {
						unset($group->dependencies[$key2]);
						continue;
					}
				}
				if ($group->dependencies->isEmpty()) {
					unset($groups[$key]);
					continue;
				}
				$groupsGlob[] = $group;
			}
			if ($groups->isEmpty()) {
				unset($companies[$keyG]);
				continue;
			}
			$company->groups = $groupsGlob;
		}
		return $this->success($companies);
	}


	public function getInfoTotal()
	{
		try {

			$fechaInicio = request()->get('pd');
			$fechaFin = request()->get('ud');

			$filtroDiarioFecha = function ($query) use ($fechaInicio, $fechaFin) {
				$query->whereBetween('date', [$fechaInicio, $fechaFin])->orderBy('date');
			};

            $tipo = request()->get('tipo');
			switch ($tipo) {
                case 'Fijo':
					$funcionario =  $this->extrasService->funcionarioFijo($filtroDiarioFecha, $fechaInicio, $fechaFin);
                    //if (isset($funcionario->getData()->msg)){
                       /*  dd(isJson($funcionario)->toString());
                    if (isJson($funcionario)->toString() == "is valid JSON"){
                        return $this->error($funcionario->getData()->msg,442);
                    } */
                    //dd($funcionario);
					return $this->success($this->extrasService->calcularExtras($funcionario));
					break;
                case 'Rotativo':
                    $funcionario = $this->extrasService->funcionarioRotativo($filtroDiarioFecha);
                    //dd( isJson($funcionario)->toString() == "is valid JSON");
                    /* if (isJson($funcionario)->toString() == "is valid JSON"){
                        return $this->error($funcionario->getData()->msg,206);
                    } */
					return $this->success($this->extrasService->calcularExtras($funcionario));
					break;
				default:
					return abort(404);
					break;
			}
		} catch (\Throwable $th) {
			//return $th;
			return $th->getMessage() . ' msg: ' . $th->getLine() . ' ' . $th->getFile();
		}
	}

	public function store()
	{
		try {

			$atributos = request()->validate([
				'person_id' => 'required',
				'date' => 'required',
				'ht' => 'required',
				'hed' => 'required',
				'hen' => 'required',
				'hedfd' => 'required',
				'hedfn' => 'required',
				'rn' => 'required',
				'rf' => 'required',
				'hed_reales' => 'required',
				'hen_reales' => 'required',
				'hedfd_reales' => 'required',
				'hedfn_reales' => 'required',
				'rn_reales' => 'required',
				'rf_reales' => 'required',
			]);
			ExtraHourReport::create($atributos);
			$hed_reales = request()->get('hed_reales');
			$hen_reales = request()->get('hen_reales');
			$hedfd_reales = request()->get('hedfd_reales');
			$hedfn_reales = request()->get('hedfn_reales');
			$sum = ($hed_reales + $hen_reales + $hedfd_reales + $hedfn_reales);
			$lunch = Lunch::where('person_id', request()->get('person_id'))->first();
			if($sum >= 3.5){
				if (isset($lunch)) {
					$lunch->update(['apply' => ('Si')]);
				}
			} else {
				if (isset($lunch)) {
					Deduction::create([
						'person_id' => request()->get('person_id'),
						'countable_deduction_id' => 5,
						'value' => $lunch->value
					]);
					$lunch->update(['apply' => ('No')]);
				}
			}
			return response()->json(['message' => 'Horas extras validadas correctamente']);
		} catch (\Throwable $th) {
			//throw $th;
			return response($th->getMessage());
		}
	}
	public function update($id, Request $request)
	{
		try {
			$validada = ExtraHourReport::findOrFail($id);

			//code...
			$atributos = request()->validate([
				'person_id' => 'required',
				'date' => 'required',
				'ht' => 'required',
				'hed' => 'required',
				'hen' => 'required',
				'hedfd' => 'required',
				'hedfn' => 'required',
				'rn' => 'required',
				'rf' => 'required',
				'hed_reales' => 'required',
				'hen_reales' => 'required',
				'hedfd_reales' => 'required',
				'hedfn_reales' => 'required',
				'rn_reales' => 'required',
				'rf_reales' => 'required',
			]);
			$validada->update($atributos);
			$hed = request()->get('hed');
			$hen = request()->get('hen');
			$hedfd = request()->get('hedfd');
			$hedfn = request()->get('hedfn');
			$sum = ($hed + $hen + $hedfd + $hedfn);
			$lunch = Lunch::where('person_id', request()->get('person_id'))->where('state', 'Activo')->first();
			if($sum >= 3.5){
				if (isset($lunch)) {
					Deduction::create([
						'person_id' => request()->get('person_id'),
						'countable_deduction_id' => 5,
						'value' => $lunch->value
					]);
					$lunch->update(['apply' => ('Si')]);
				}
			} else {
				if (isset($lunch)) {
					$lunch->update(['apply' => ('No')]);
				}
			}
			return response()->json(['message' => 'Horas extras validadas correctamente']);
		} catch (\Throwable $th) {
			//throw $th;
			return response($th->getMessage());
		}
	}

	public function getDataValid($person_id, $fecha)
	{
		$validada = ExtraHourReport::where('date', $fecha)->where('person_id', $person_id)->orderBy('date')->first();
		return $this->success($validada);
	}
}
