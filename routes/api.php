<?php

/* use App\Http\Controllers\AuthController; */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompensationFundController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\DisabilityLeaveController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\FixedTurnController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PayrollFactorController;
use App\Http\Controllers\PensionFundController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RotatingTurnController;
use App\Http\Controllers\SeveranceFundController;
use App\Http\Controllers\WorkContractTypeController;
use App\Http\Controllers\ZonesController;
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

		Route::get('/get-menu',  [MenuController::class, 'getByPerson']);
		Route::post('/save-menu',  [MenuController::class, 'store']);
		Route::post('/jobs/set-state/{id}',  [JobController::class, 'setState']);
		Route::get('/payroll-factor-people',  [PayrollFactorController::class, 'indexByPeople']);
		
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
		Route::resource('zones', ZonesController::class);

		/* Paginations */
		Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);		
		Route::get('paginateMunicipality', [MunicipalityController::class, 'paginate']);
	}
);
