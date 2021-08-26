<?php

/* use App\Http\Controllers\AuthController; */

use App\Http\Controllers\ArlController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonificationsController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompensationFundController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\DisabilityLeaveController;
use App\Http\Controllers\DotationController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\FixedTurnController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InventaryDotationController;
use App\Http\Controllers\InventaryDotationGroupController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LateArrivalController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PayrollFactorController;
use App\Http\Controllers\PensionFundController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductDotationTypeController;
use App\Http\Controllers\RotatingTurnController;
use App\Http\Controllers\RrhhActivityController;
use App\Http\Controllers\RrhhActivityTypeController;
use App\Http\Controllers\SeveranceFundController;
use App\Http\Controllers\WorkContractTypeController;
use App\Models\ProductDotationType;
use App\Http\Controllers\ZonesController;
use App\Models\Countable_income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/asistencia/validar', [AsistenciaController::class, 'validar']);


Route::prefix("auth")->group(
	function () {
		Route::post("login", [AuthController::class, "login"]);
		Route::post("register", [AuthController::class, "register"]);
		Route::middleware("auth.jwt")->group(function () {
			Route::post("logout", [AuthController::class, "logout"]);
			Route::post("refresh", [AuthController::class, "refresh"]);
			Route::post("me", [AuthController::class, "me"]);
			Route::get("renew", [AuthController::class, "renew"]);
			Route::get("change-password", [
				AuthController::class,
				"changePassword",
			]);
		});
	}
);
Route::group(
	[
		"middleware" => ["api"],
	],
	function ($router) {
		Route::get("people-paginate", [PersonController::class, "indexPaginate"]);
		Route::get("people-all", [PersonController::class, "getAll"]);

		Route::get('/get-menu',  [MenuController::class, 'getByPerson']);
		Route::post('/save-menu',  [MenuController::class, 'store']);
		Route::post('/jobs/set-state/{id}',  [JobController::class, 'setState']);
		Route::get('/payroll-factor-people',  [PayrollFactorController::class, 'indexByPeople']);


		/** Rutas inventario dotacion rrhh */
		Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
		Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
		Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
		Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);
		Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);
		/** end*/

		/** Rutas actividades rrhh */
		Route::get('/rrhh-activity-people/{id}',  [RrhhActivityController::class, 'getPeople']);
		Route::get('/rrhh-activity/cancel/{id}',  [RrhhActivityController::class, 'cancel']);
		Route::post('/rrhh-activity-types/set',  [RrhhActivityTypeController::class, 'setState']);
		/** end*/

		

		/** Rutas del módulo de llegadas tarde */
		Route::get('/late_arrivals/data/{fechaInicio}/{fechaFin}', [LateArrivalController::class, 'getData'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		Route::get('/late_arrivals/statistics/{fechaInicio}/{fechaFin}', [LateArrivalController::class,'statistics']);

		/** Resources */

		Route::resource('dependencies', DependencyController::class);
		Route::resource('company', CompanyController::class);
		Route::resource('positions', PositionController::class);
		Route::resource('work-contract-type', WorkContractTypeController::class);
		Route::resource('fixed-turns', FixedTurnController::class);
		Route::resource('rotating-turns', RotatingTurnController::class);
		Route::resource('severance-funds', SeveranceFundController::class);
		Route::resource('pension-funds', PensionFundController::class);
		Route::resource('compensation-funds', CompensationFundController::class);
		Route::resource('eps', EpsController::class);
		Route::resource('people', PersonController::class);
		Route::resource('group', GroupController::class);
		Route::resource('departments', DepartmentController::class);
		Route::resource('municipalities', MunicipalityController::class);
		Route::resource('jobs', JobController::class);
		Route::resource('disability-leaves', DisabilityLeaveController::class);
		Route::resource('payroll-factor', PayrollFactorController::class);
		Route::resource('inventary-dotation', InventaryDotationController::class);
		Route::resource('product-dotation-types', ProductDotationTypeController::class);
		Route::resource('dotations', DotationController::class);
		Route::resource('rrhh-activity-types', RrhhActivityTypeController::class);
		Route::resource('rrhh-activity', RrhhActivityController::class);
		Route::resource('late-arrivals', LateArrivalController::class);
		Route::resource('zones', ZonesController::class);
		Route::resource('bonifications', BonificationsController::class);
		Route::resource('countable_incomes', Countable_income::class);
		Route::resource('arl', ArlController::class);
		
		/* Route::resource('inventary-dotation-group', ProductDotationType::class); */

		/* Paginations */
		Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);		
		Route::get('paginateMunicipality', [MunicipalityController::class, 'paginate']);

		Route::get('person/{id}', [PersonController::class, 'basicData']);
		Route::get('basicData/{id}', [PersonController::class, 'basicDataForm']);
		Route::post('updatebasicData/{id}', [PersonController::class, 'updateBasicData']);
		Route::get('salary/{id}', [PersonController::class, 'salary']);
		Route::post('salary', [PersonController::class, 'updateSalaryInfo']);
		Route::get('enterpriseData/{id}', [PersonController::class, 'enterpriseData']);
		Route::post('enterpriseData', [PersonController::class, 'updateEnterpriseData']);
		Route::get('afiliation/{id}', [PersonController::class, 'afiliation']);
		Route::post('updateAfiliation/{id}', [PersonController::class, 'updateAfiliation']);
		
		Route::get('countable_income', [BonificationsController::class, 'countable_income']);
		
		Route::get('epss', [PersonController::class, 'epss']);
		Route::get('fixed_turn', [PersonController::class, 'fixed_turn']);
/* 		Route::resource('bonusData', [BonificationsController::class]); */
	}
);
