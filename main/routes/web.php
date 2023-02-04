<?php

use App\Models\ApuService;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $company = Company::first();
    $image = $company->page_heading;
    $data = ApuService::with([
        "city",
        "person" => function ($q) {
            $q->select('id', DB::raw('concat(first_name, " ", first_surname) as name'));
        },
        "thirdparty" => function ($q) {
            $q->select('id', DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as name'));
        },
        "dimensionalValidation" => function ($q) {
            $q->select("*")
                ->with('profiles');
        },
        "dimensionalValidation.travelEstimationDimensionalValidations" => function ($q) {
            $q->select("*");
        },
        "assembliesStartUp" => function ($q) {
            $q->select("*")
                ->with('profiles');
        },
        "assembliesStartUp.travelEstimationAssembliesStartUp" => function ($q) {
            $q->select("*");
        }
    ])
        ->where("id", 3)
        ->first();
    $datosCabecera = (object) array(
        'Titulo' => 'APU Servicio',
        'Codigo' => $data->code,
        'Fecha' => $data->created_at,
        'CodigoFormato' => $data->format_code
    );
    //return $data;
    //return View::make('pdf/apu_service')->with(compact('company', 'datosCabecera', 'image'));
    return view('pdf/apu_service')->with(compact('data', 'company', 'datosCabecera', 'image'));
});
