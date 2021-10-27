<?php

/* use App\Http\Controllers\AuthController; */

use App\Http\Controllers\AccountPlanController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ArlController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AttentionCallController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountsController;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\BonificationsController;
use App\Http\Controllers\CenterCostController;
use App\Http\Controllers\CiiuCodeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyPaymentConfigurationController;
use App\Http\Controllers\CompensationFundController;
use App\Http\Controllers\Countable_incomeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\DianAddressController;
use App\Http\Controllers\DisabilityLeaveController;
use App\Http\Controllers\DisciplinaryProcessController;
use App\Http\Controllers\DocumentTypesController;
use App\Http\Controllers\DotationController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\EgressTypesController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\ExternalProcessController;
use App\Http\Controllers\ExtraHoursController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\FixedAssetTypeController;
use App\Http\Controllers\FixedTurnController;
use App\Http\Controllers\FixedTurnDiaryController;
use App\Http\Controllers\FixedTurnHourController;
use App\Http\Controllers\GeometryController;
use App\Http\Controllers\GeometryMeasureController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\IngressTypesController;
use App\Http\Controllers\InternalProcessController;
use App\Http\Controllers\InventaryDotationController;
use App\Http\Controllers\InventaryDotationGroupController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LateArrivalController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LunchControlller;
use App\Http\Controllers\MachineToolController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\MemorandumController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PayrollFactorController;
use App\Http\Controllers\PensionFundController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductDotationTypeController;
use App\Http\Controllers\ReporteHorariosController;
use App\Http\Controllers\RotatingTurnController;
use App\Http\Controllers\RrhhActivityController;
use App\Http\Controllers\RrhhActivityTypeController;
use App\Http\Controllers\SeveranceFundController;
use App\Http\Controllers\MemorandumTypesController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayVacationController;
use App\Http\Controllers\PrettyCashController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\RetentionTypeController;
use App\Http\Controllers\RiskTypesController;
use App\Http\Controllers\RotatingTurnDiaryController;
use App\Http\Controllers\RotatingTurnHourController;
use App\Http\Controllers\SalaryTypesController;
use App\Http\Controllers\TaxiCityController;
use App\Http\Controllers\TaxiControlller;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\ThirdPartyFieldController;
use App\Http\Controllers\ThirdPartyPersonController;
use App\Http\Controllers\TravelExpenseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VisaTypeController;
use App\Http\Controllers\WinningListController;
use App\Http\Controllers\WorkContractController;
use App\Http\Controllers\WorkContractTypeController;
use App\Http\Controllers\ZonesController;
use App\Models\AttentionCall;
use App\Models\CompanyPaymentConfiguration;
use App\Models\Countable_income;
use App\Models\DisabilityLeave;
use App\Models\DocumentTypes;
use App\Models\RetentionType;
use App\Models\TravelExpense;
use App\Models\User;
use App\Models\WorkContract;
use App\Models\WorkContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

Route::get('/', function () {

	$exitCode = Artisan::call('config:clear');

	$exitCode = Artisan::call('cache:clear');

	$exitCode = Artisan::call('config:cache');

	return 'DONE'; //Return anything

});
Route::get('/generate-users', function () {

	$people  = DB::select('SELECT p.* from people p
	where not EXISTS(
	SELECT u.id from users u  where u.person_id = p.id
	)');


	foreach ($people as $person ) {
		# code....
		User::create([
			"person_id" => $person->id,
			"usuario" => $person->identifier,
			"password" => Hash::make($person->identifier),
			"change_password" => 1,
		]);

	}

	return 'DONE'; //Return anything

});

Route::get('/image', function () {

	$path = Request()->get('path');
	if ($path) {
		$path = storage_path('app/public').'/'.$path;
		return response()->file($path);
	}
	return 'path not found';
});


Route::get('/file', function () {
	$path = Request()->get('path');
	$download = storage_path('app/' . $path);
	if ($path) {
		return response()->download($download);
	}
	return 'path not found';
});

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
		Route::get("payroll-nex-mouths", [PayrollController::class, "nextMonths"]);
		Route::get("people-paginate", [PersonController::class, "indexPaginate"]);
		Route::get("people-all", [PersonController::class, "getAll"]);

		Route::get('/get-menu',  [MenuController::class, 'getByPerson']);
		Route::post('/save-menu',  [MenuController::class, 'store']);

		Route::get('jobs-preview',  [JobController::class, 'getPreview']);

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

		/** Rutas del módulo de reporte de horarios */
		Route::get('/reporte/horarios/{fechaInicio}/{fechaFin}/turno_rotativo', [ReporteHorariosController::class, 'getDatosTurnoRotativo'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);
		Route::get('/reporte/horarios/{fechaInicio}/{fechaFin}/turno_fijo', [ReporteHorariosController::class, 'fixed_turn_diaries'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);
		Route::get('/download/horarios/{fechaInicio}/{fechaFin}', [ReporteHorariosController::class, 'download'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		/** Rutas del módulo de llegadas tarde */
		Route::get('/late_arrivals/data/{fechaInicio}/{fechaFin}', [LateArrivalController::class, 'getData'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);
		Route::get('late-arrivals/download/{inicio?}/{fin?}', [LateArrivalController::class, 'download']);

		Route::get('/horarios/datos/generales/{semana}', [RotatingTurnHourController::class, 'getDatosGenerales']);
		Route::get('download-applicants/{id}', [ApplicantController::class, 'donwloadCurriculum']);


		Route::get('/late_arrivals/statistics/{fechaInicio}/{fechaFin}', [LateArrivalController::class, 'statistics']);
		Route::get('/fixed-turn-hours', [FixedTurnHourController::class, 'index']);
		Route::post('/rotating-turns/change-state/{id}', [RotatingTurnController::class, 'changeState']);
		Route::post('/fixed-turns/change-state/{id}', [FixedTurnController::class, 'changeState']);
		/** Resources */
		Route::get('person/train', [PersonController::class, 'train']);
		Route::get('account-plan-balance', [AccountPlanController::class, 'listBalance']);
		Route::get('account-plan-list', [AccountPlanController::class, 'list']);

		Route::post('travel-expense/update/{id}', [TravelExpenseController::class, 'update']);
		Route::get('travel-expense/pdf/{id}', [TravelExpenseController::class, 'pdf']);
		/** ---------  horas extras */
		Route::get('/horas_extras/turno_rotativo/{fechaInicio}/{fechaFin}/{tipo}', [ExtraHoursController::class, 'getDataRotative'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);
		Route::post('funcionario/getInfoTotal', [ExtraHoursController::class, 'getInfoTotal']);
		Route::post('horas_extras/crear', [ExtraHoursController::class, 'store']);
		Route::put('horas_extras/{id}/update', [ExtraHoursController::class, 'update']);
		Route::get('horas_extras/datos/validados/{person_id}/{fecha}', [ExtraHoursController::class, 'getDataValid'])->where([
			'fecha' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		# Fijo	---

		/**----- end horas extras */
		/**                PayRoll */

		Route::get('nomina/pago/funcionarios/{inicio?}/{fin?}', [PayrollController::class, 'payPeople']);
		Route::get('nomina/pago/{inicio?}/{fin?}', [PayrollController::class, 'getPayrollPay']);

		/**               End Payroll */
		Route::resource('third-party-fields', ThirdPartyFieldController::class);

		/**End */
		Route::resource('applicants', ApplicantController::class);
		Route::resource('pretty-cash', PrettyCashController::class);
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
		Route::resource('countable_incomes', Countable_incomeController::class);
		Route::resource('arl', ArlController::class);
		/* Route::resource('inventary-dotation-group', ProductDotationType::class); */
		Route::resource('work_contracts', WorkContractController::class);
		Route::resource('memorandum', MemorandumController::class);
		Route::resource('type_memorandum', MemorandumTypesController::class);
		Route::resource('disciplinary_process', DisciplinaryProcessController::class);
		Route::resource('salaryTypes', SalaryTypesController::class);
		Route::resource('rotating-hour', RotatingTurnHourController::class);
		Route::resource('rotating-hour-diary', RotatingTurnDiaryController::class);
		Route::resource('fixed-hour-diary', FixedTurnDiaryController::class);
		Route::resource('documentTypes', DocumentTypesController::class);
		Route::resource('countries', CountryController::class);
		Route::resource('risk', RiskTypesController::class);
		Route::resource('egress_types', EgressTypesController::class);
		Route::resource('ingress_types', IngressTypesController::class);
		Route::resource('banks', BanksController::class);
		Route::resource('banksAccount', BankAccountsController::class);
		Route::resource('account_plan', AccountPlanController::class);
		Route::resource('center_cost', CenterCostController::class);
		Route::resource('hotels', HotelController::class);
		Route::resource('taxis', TaxiControlller::class);
		Route::resource('travel-expense', TravelExpenseController::class);
		Route::resource('taxi-city', TaxiCityController::class);
		Route::resource('city', CityController::class);
		Route::resource('companyPayment', CompanyPaymentConfigurationController::class);
		Route::resource('loan', LoanController::class);
		Route::resource('fixed_asset', FixedAssetController::class);
		Route::resource('fixed_asset_type', FixedAssetTypeController::class);
		Route::resource('lunch', LunchControlller::class);
		Route::resource('professions', ProfessionController::class);
		Route::resource('third-party', ThirdPartyController::class);
		Route::resource('third-party-person', ThirdPartyPersonController::class);
		Route::resource('winnings-list', WinningListController::class);
		Route::resource('ciiu-code', CiiuCodeController::class);
		Route::resource('dian-address', DianAddressController::class);
		Route::resource('pay-vacation', PayVacationController::class);
		Route::resource('retention-type', RetentionTypeController::class);
		Route::resource('attention-call', AttentionCallController::class);
		Route::resource('cities', CityController::class);
		Route::resource('drivingLicenses', DrivingLicenseController::class);
		Route::resource('visa-types', VisaTypeController::class);
		Route::resource('alerts', AlertController::class);
        Route::resource('geometry', GeometryController::class);
        Route::resource('measure', MeasureController::class);
        Route::resource('geometry-measure', GeometryMeasureController::class);
        Route::resource('materials', MaterialController::class);
        Route::resource('units', UnitController::class);
        Route::resource('machinestools', MachineToolController::class);
        Route::resource('internalprocesses', InternalProcessController::class);
        Route::resource('externalprocesses', ExternalProcessController::class);



		/* Paginations */
		Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);
		Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);
		Route::get('paginateMunicipality', [MunicipalityController::class, 'paginate']);
		Route::get('paginateContractType', [WorkContractTypeController::class, 'paginate']);
		Route::get('paginateSalaryType', [SalaryTypesController::class, 'paginate']);
		Route::get('paginateDocumentType', [DocumentTypesController::class, 'paginate']);
		Route::get('paginateCountries', [CountryController::class, 'paginate']);
		Route::get('paginateArl', [ArlController::class, 'paginate']);
		Route::get('paginatePensionFun', [PensionFundController::class, 'paginate']);
		Route::get('paginateCompensationFund', [CompensationFundController::class, 'paginate']);
		Route::get('paginateNoveltyTypes', [DisabilityLeaveController::class, 'paginate']);
		Route::get('paginateRiskTypes', [RiskTypesController::class, 'paginate']);
		Route::get('paginateSeveranceFunds', [SeveranceFundController::class, 'paginate']);
		Route::get('paginateEgressTypes', [EgressTypesController::class, 'paginate']);
		Route::get('paginateIngressTypes', [IngressTypesController::class, 'paginate']);
		Route::get('paginateBanks', [BanksController::class, 'paginate']);
		Route::get('paginateBankAccount', [BankAccountsController::class, 'paginate']);
		Route::get('paginateProfessions', [ProfessionController::class, 'paginate']);
		Route::get('paginateFixedAssetType', [FixedAssetTypeController::class, 'paginate']);
		Route::get('paginateRetentionType', [RetentionTypeController::class, 'paginate']);
		Route::get('paginateHotels', [HotelController::class, 'paginate']);
		Route::get('paginateTaxis', [TaxiControlller::class, 'paginate']);
		Route::get('paginateCities', [CityController::class, 'paginate']);
		Route::get('paginateDrivingLicences', [DrivingLicenseController::class, 'paginate']);
		Route::get('paginateVisaTypes', [VisaTypeController::class, 'paginate']);
        Route::get('paginateMaterial', [MaterialController::class, 'paginate']);
        Route::get('paginateMachines', [MachineToolController::class, 'paginate']);

		/* Paginations */

		Route::get('person/{id}', [PersonController::class, 'basicData']);
		Route::get('basicData/{id}', [PersonController::class, 'basicDataForm']);
		Route::post('updatebasicData/{id}', [PersonController::class, 'updateBasicData']);
		Route::get('salary/{id}', [PersonController::class, 'salary']);
		Route::post('salary', [PersonController::class, 'updateSalaryInfo']);
		Route::get('afiliation/{id}', [PersonController::class, 'afiliation']);
		Route::post('updateAfiliation/{id}', [PersonController::class, 'updateAfiliation']);
		Route::get('epss', [PersonController::class, 'epss']);
		Route::get('fixed_turn', [PersonController::class, 'fixed_turn']);
		Route::post('enterpriseData', [WorkContractController::class, 'updateEnterpriseData']);
		Route::get('countable_income', [BonificationsController::class, 'countable_income']);
		Route::get('contractsToExpire', [WorkContractController::class, 'contractsToExpire']);
		Route::get('preLiquidado', [WorkContractController::class, 'getPreliquidated']);
		Route::get('liquidado/{id}', [WorkContractController::class, 'getLiquidated']);
		Route::get('periodoP', [WorkContractController::class, 'getTrialPeriod']);
		Route::get('memorandums', [MemorandumController::class, 'getMemorandum']);
		Route::get('ListLimitated', [memorandumTypesController::class, 'getListLimitated']);
		Route::get('process/{id}', [DisciplinaryProcessController::class, 'process']);
		Route::get('companyData', [CompanyController::class, 'getBasicData']);
		Route::post('saveCompanyData', [CompanyController::class, 'saveCompanyData']);
		Route::get('proyeccion_pdf/{id}', [LoanController::class, 'loanpdf']);
		// Route::post('attentionCall', [MemorandumController::class, 'attentionCall']);
		Route::post('approve/{id}', [TravelExpenseController::class, 'approve']);
		Route::get('all-zones', [ZonesController::class, 'allZones']);
		Route::get('all-municipalities', [MunicipalityController::class, 'allMunicipalities']);
		Route::get('account-plan', [AccountPlanController::class, 'accountPlan']);
		Route::get('third-parties-list', [ThirdPartyController::class, 'thirdParties']);
		Route::put('state-change', [LunchControlller::class, 'activateOrInactivate']);
		Route::get('filter-all-depencencies', [DependencyController::class, 'dependencies']);
		Route::get('filter-all-positions', [PositionController::class, 'positions']);
		Route::get('alert/{id}', [AttentionCallController::class, 'callAlert']);
		Route::get('descargo/{id}', [DisciplinaryProcessController::class, 'descargoPdf']);
		Route::put('activate-inactivate', [ThirdPartyController::class, 'changeState']);
		Route::get('fields-third', [ThirdPartyController::class, 'getFields']);
		Route::put('liquidateOrActivate/{id}', [PersonController::class, 'liquidateOrActivate']);



	}
);
