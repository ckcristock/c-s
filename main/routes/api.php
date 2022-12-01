<?php

/* use App\Http\Controllers\AuthController; */

use App\Http\Controllers\AccountPlanController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApuController;
use App\Http\Controllers\ApuPartController;
use App\Http\Controllers\ApuProfileController;
use App\Http\Controllers\ApuServiceController;
use App\Http\Controllers\ApuSetController;
use App\Http\Controllers\ArlController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AttentionCallController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountsController;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\BenefitIncomeController;
use App\Http\Controllers\BenefitNotIncomeController;
use App\Http\Controllers\BonificationsController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BonusPersonController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CalculationBaseController;
use App\Http\Controllers\CenterCostController;
use App\Http\Controllers\CiiuCodeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContractTermController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyPaymentConfigurationController;
use App\Http\Controllers\CompensationFundController;
use App\Http\Controllers\CountableDeductionController;
use App\Http\Controllers\CountableIncomeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CutLaserMaterialController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\DianAddressController;
use App\Http\Controllers\DisabilityLeaveController;
use App\Http\Controllers\DisciplinaryProcessController;
use App\Http\Controllers\DisplacementController;
use App\Http\Controllers\DocumentTypesController;
use App\Http\Controllers\DotationController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\EgressTypesController;
use App\Http\Controllers\ElectronicPayrollController;
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
use App\Http\Controllers\IndirectCostController;
use App\Http\Controllers\IngressTypesController;
use App\Http\Controllers\InternalProcessController;
use App\Http\Controllers\InventaryDotationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LateArrivalController;
use App\Http\Controllers\LoanController;
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
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ProductDotationTypeController;
use App\Http\Controllers\ReporteHorariosController;
use App\Http\Controllers\RotatingTurnController;
use App\Http\Controllers\RrhhActivityController;
use App\Http\Controllers\RrhhActivityTypeController;
use App\Http\Controllers\SeveranceFundController;
use App\Http\Controllers\MemorandumTypeController;
use App\Http\Controllers\PayrollConfigController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollOvertimeController;
use App\Http\Controllers\PayrollParametersController;
use App\Http\Controllers\PayrollPaymentController;
use App\Http\Controllers\PayVacationController;
use App\Http\Controllers\PrettyCashController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\RetentionTypeController;
use App\Http\Controllers\RegimeTypeController;
use App\Http\Controllers\FiscalResponsibilityController;
use App\Http\Controllers\RiskTypesController;
use App\Http\Controllers\RotatingTurnDiaryController;
use App\Http\Controllers\RotatingTurnHourController;
use App\Http\Controllers\SalaryTypesController;
use App\Http\Controllers\TaxiCityController;
use App\Http\Controllers\TaxiControlller;
use App\Http\Controllers\ThicknessController;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\ThirdPartyFieldController;
use App\Http\Controllers\ThirdPartyPersonController;
use App\Http\Controllers\TravelExpenseController;
use App\Http\Controllers\TravelExpenseEstimationController;
use App\Http\Controllers\PersonInvolvedController;
use App\Http\Controllers\TravelExpenseEstimationValuesController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VisaTypeController;
use App\Http\Controllers\WinningListController;
use App\Http\Controllers\WorkContractController;
use App\Http\Controllers\WorkContractTypeController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\LunchValueController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\LayoffsCertificateController;
use App\Http\Controllers\LiquidacionesController;
use App\Http\Controllers\LiquidationsController;
use App\Http\Controllers\ReasonWithdrawalController;
use App\Http\Controllers\WorkCertificateController;
use App\Http\Controllers\BodegasController;
use App\Http\Controllers\CategoriaNuevaController;
use App\Http\Controllers\ListaComprasController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PayrollManagerController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TaskTypeController;
use App\Models\Business;
use App\Models\BusinessBudget;
use App\Models\ThirdParty;
use App\Models\User;
use App\Models\Bonus;
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

    return 'DONE'; //Return anythingb

});
Route::get('/generate-users', function () {

    $people  = DB::select('SELECT p.* from people p
	where not EXISTS(
	SELECT u.id from users u  where u.person_id = p.id
	)');


    foreach ($people as $person) {
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
        $path = public_path('app/public') . '/' . $path;
        return response()->file($path);
    }
    return 'path not found';
});


Route::get('/file', function () {
    $path = Request()->get('path');
    $download = public_path('app' . '/' . $path);
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
        Route::get("people-with-dni", [PersonController::class, "peoplesWithDni"]);
        Route::get("validar-cedula/{documento}", [PersonController::class, "validarCedula"]);
        Route::get('/get-menu',  [MenuController::class, 'getByPerson']);
        Route::post('/save-menu',  [MenuController::class, 'store']);

        Route::get('jobs-preview',  [JobController::class, 'getPreview']);

        Route::post('/jobs/set-state/{id}',  [JobController::class, 'setState']);
        Route::get('/payroll-factor-people',  [PayrollFactorController::class, 'indexByPeople']);
        Route::get('/payroll-factor-people-count',  [PayrollFactorController::class, 'count']);

        Route::get('electronic-payroll/{id}',  [ElectronicPayrollController::class, 'getElectronicPayroll']);
        Route::get('electronic-payroll-paginate/{id}',  [ElectronicPayrollController::class, 'paginate']);
        Route::get('electronic-payroll-statistics/{id}',  [ElectronicPayrollController::class, 'statistics']);
        Route::delete('electronic-payroll/{id}', [ElectronicPayrollController::class, 'deleteElectroincPayroll']);

        /*CONFIG NOMINA*/

        Route::get('parametrizacion/nomina/all', [PayrollConfigController::class, 'getParametrosNomina']);
        Route::get('parametrizacion/nomina/extras', [PayrollConfigController::class, 'horasExtrasDatos']);
        Route::get('parametrizacion/nomina/incapacidades', [PayrollConfigController::class, 'incapacidadesDatos']);
        Route::get('parametrizacion/nomina/novelties', [PayrollConfigController::class, 'novedadesList']);
        Route::get('parametrizacion/nomina/parafiscales', [PayrollConfigController::class, 'parafiscalesDatos']);
        Route::get('parametrizacion/nomina/riesgos', [PayrollConfigController::class, 'riesgosArlDatos']);
        Route::get('parametrizacion/nomina/ssocial_empresa', [PayrollConfigController::class, 'sSocialEmpresaDatos']);
        Route::get('parametrizacion/nomina/ssocial_funcionario', [PayrollConfigController::class, 'sSocialFuncionarioDatos']);
        Route::get('parametrizacion/nomina/income', [PayrollConfigController::class, 'incomeDatos' ]);
        Route::get('parametrizacion/nomina/deductions', [PayrollConfigController::class, 'deductionsDatos' ]);
        Route::get('parametrizacion/nomina/liquidations', [PayrollConfigController::class, 'liquidationsDatos' ]);
        Route::get('parametrizacion/nomina/salarios-subsidios', [PayrollConfigController::class, 'SalariosSubsidiosDatos' ]);

        /**ACTUALIZAR PARAMETROS CONFIG NOMINA */
        Route::put('parametrizacion/nomina/extras/update/{id}', [PayrollConfigController::class, 'horasExtrasUpdate']);
        Route::put('parametrizacion/nomina/seguridad-social-persona/update/{id}', [PayrollConfigController::class, 'sSocialPerson']);
        Route::put('parametrizacion/nomina/seguridad-social-company/update/{id}', [PayrollConfigController::class, 'sSocialCompany']);
        Route::put('parametrizacion/nomina/riesgos-arl/update/{id}', [PayrollConfigController::class, 'riesgosArlUpdate']);
        Route::put('parametrizacion/nomina/parafiscales/update/{id}', [PayrollConfigController::class, 'parafiscalesUpdate']);
        Route::put('parametrizacion/nomina/incapacidades/update/{id}', [PayrollConfigController::class, 'incapacidadesUpdate']);
        Route::post('parametrizacion/nomina/income/update', [PayrollConfigController::class, 'createUptadeIncomeDatos' ]);
        Route::post('parametrizacion/nomina/deductions/update', [PayrollConfigController::class, 'createUpdateDeductionsDatos' ]);
        Route::post('parametrizacion/nomina/liquidations/update', [PayrollConfigController::class, 'createUpdateLiquidationsDatos' ]);
        Route::post('parametrizacion/nomina/salarios-subsidios/update', [PayrollConfigController::class, 'createUpdateSalariosSubsidiosDatos' ]);
        /**/

        /** Rutas inventario dotacion rrhh */
        Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
        Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
        Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
        Route::get('/get-selected',  [InventaryDotationController::class, 'getSelected']);
        Route::get('/get-total-inventary',  [InventaryDotationController::class, 'getTotatInventary']);
        Route::get('/inventary-dotation-stock-epp',  [InventaryDotationController::class, 'getInventaryEpp']);
        Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);
        Route::post('/dotations-approve/{id}',  [DotationController::class, 'approve']);
        Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);
        Route::get('/dotations-list-product',  [DotationController::class, 'getListProductsDotation']);

        Route::get('dotations/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'download']);
        Route::get('downloadeliveries/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'downloadeliveries']);


        /** end*/

        /** Rutas actividades rrhh */
        Route::get('/rrhh-activity-people/{id}',  [RrhhActivityController::class, 'getPeople']);
        Route::get('/rrhh-activity/cancel/{id}',  [RrhhActivityController::class, 'cancel']);
        Route::post('/rrhh-activity/cancelCycle/{code}',  [RrhhActivityController::class, 'cancelCycle']);
        Route::post('/rrhh-activity-types/set',  [RrhhActivityTypeController::class, 'setState']);
        // Route::put('/rrhh-activity/{id}', [RrhhActivityController::class, 'update']);
        Route::get('/rrhh-activity-types-all',  [RrhhActivityTypeController::class, 'all']);
        Route::get('/rrhh-activity-types-actives',  [RrhhActivityTypeController::class, 'actives']);
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
        Route::get('download-vacation/{id}', [PayVacationController::class, 'download']);


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

        /** Rutas PayRoll */

        Route::get('nomina/pago/funcionario/{identidad}', [PayrollController::class, 'getFuncionario']);
        Route::get('nomina/pago/funcionarios/{inicio?}/{fin?}', [PayrollController::class, 'payPeople']);
        Route::get('nomina/pago/{inicio?}/{fin?}', [PayrollController::class, 'getPayrollPay']);

        Route::get('payroll/overtimes/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getExtrasTotales']);

        Route::get('payroll/salary/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getSalario']);
        Route::get('payroll/factors/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getNovedades']);
        Route::get('payroll/incomes/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getIngresos']);
        Route::get('payroll/retentions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getRetenciones']);
        Route::get('payroll/deductions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getDeducciones']);
        Route::get('payroll/net-pay/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getPagoNeto']);
        /* 	Route::get('payroll/social-security/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getPorcentajes']); */
        Route::get('payroll/social-security/person', [PayrollController::class, 'getPorcentajes']);
        Route::get('payroll/history/payments', [PayrollPaymentController::class, 'getPagosNomina']);


        Route::get('payroll/security/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getSeguridad']);
        Route::get('payroll/provisions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getProvisiones']);
        Route::post('payroll/pay', [PayrollController::class, 'store']);
        Route::post('payroll/report/{id}', [PayrollController::class, 'reportDian']);



        /** End Payroll */
        Route::resource('third-party-fields', ThirdPartyFieldController::class);
        Route::put('changeStateField/{id}', [ThirdPartyFieldController::class, 'changeState']);


        /**
         * PARAMETRIZACION NOMINA
         */
        Route::get('params/payroll/overtimes/percentages', [PayrollOvertimeController::class, 'horasExtrasPorcentajes']);
        Route::get('params/payroll/ssecurity_company/percentages/{id}', [PayrollParametersController::class, 'porcentajesSeguridadRiesgos']);



        /**End */
        Route::resource('applicants', ApplicantController::class);
        Route::resource('bodegas', BodegasController::class)->only(['index', 'store', 'show']);

        Route::post('bodegas-activar-inactivar', [BodegasController::class,'activarInactivar']);
        Route::post('grupos-bodegas', [BodegasController::class,'storeGrupo']);
        Route::post('estibas', [BodegasController::class,'storeEstiba']);
        Route::get('bodegas-with-estibas/{id}', [BodegasController::class,'bodegasConGrupos']);
        Route::get('grupos-with-estibas/{id}', [BodegasController::class,'gruposConEstibas']);

        Route::resource('reason_withdrawal', ReasonWithdrawalController::class);
        Route::resource('work-certificate', WorkCertificateController::class);

        Route::get('download-work-certificate/{id}', [WorkCertificateController::class, 'pdf']);

        Route::resource('layoffs-certificate', LayoffsCertificateController::class);

        Route::get('download-layoffs-certificate/{id}', [LayoffsCertificateController::class, 'pdf']);


        Route::post('update-file-permission', [PersonController::class, 'updateFilePermission']);
        Route::get('get-file-permission/{id}', [PersonController::class, 'getFilePermission']);
        Route::get('third-party-person-for-third/{id}', [ThirdPartyPersonController::class, 'getThirdPartyPersonForThird']);



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

        Route::get('payroll-factor-download', [PayrollFactorController::class, 'payrollFactorDownload']);

        Route::resource('inventary-dotation', InventaryDotationController::class);
        Route::resource('product-dotation-types', ProductDotationTypeController::class);
        Route::resource('dotations', DotationController::class);
        Route::resource('rrhh-activity-types', RrhhActivityTypeController::class);
        Route::resource('rrhh-activity', RrhhActivityController::class);
        Route::resource('late-arrivals', LateArrivalController::class);
        Route::resource('zones', ZonesController::class);
        Route::resource('bonifications', BonificationsController::class);
        Route::resource('countable_incomes', CountableIncomeController::class);
        Route::resource('arl', ArlController::class);
        /* Route::resource('inventary-dotation-group', ProductDotationType::class); */
        Route::resource('work_contracts', WorkContractController::class);
        Route::resource('memorandum', MemorandumController::class);
        Route::resource('type_memorandum', MemorandumTypeController::class);
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
        Route::resource('lunch', LunchController::class);
        Route::resource('professions', ProfessionController::class);
        Route::resource('third-party', ThirdPartyController::class);
        Route::resource('third-party-person', ThirdPartyPersonController::class);
        Route::resource('winnings-list', WinningListController::class);
        Route::resource('ciiu-code', CiiuCodeController::class);
        Route::resource('dian-address', DianAddressController::class);
        Route::resource('pay-vacation', PayVacationController::class);
        Route::resource('retention-type', RetentionTypeController::class);
        Route::resource('regime-type', RegimeTypeController::class);
        Route::resource('fiscal-responsibility', FiscalResponsibilityController::class);
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
        Route::resource('countable-incomes', BenefitIncomeController::class);
        Route::resource('countable-not-incomes', BenefitNotIncomeController::class);
        Route::resource('deductions', DeductionController::class);
        Route::resource('countable_deductions', CountableDeductionController::class);
        Route::resource('indirect-cost', IndirectCostController::class);
        Route::resource('apu-parts', ApuPartController::class);
        Route::resource('apu-sets', ApuSetController::class);
        Route::resource('thicknesses', ThicknessController::class);
        Route::resource('cut-laser-material', CutLaserMaterialController::class);
        Route::resource('calculation-bases', CalculationBaseController::class);
        Route::resource('apu', ApuController::class);
        Route::resource('budgets', BudgetController::class);
        Route::resource('apu-profile', ApuProfileController::class);
        Route::resource('travel-expense-estimation', TravelExpenseEstimationController::class);
        Route::resource('travelExpenseEstimationValue', TravelExpenseEstimationValuesController::class);
        Route::resource('apu-service', ApuServiceController::class);
        Route::resource('lunch-value', LunchValueController::class);
        Route::resource('annotation', PersonInvolvedController::class);
        Route::resource('business', BusinessController::class);
        Route::resource('quotations', QuotationController::class);
        Route::resource('contract-terms', ContractTermController::class)->except(['create', 'edit']);
        Route::resource('payroll-manager', PayrollManagerController::class)->except(['create', 'edit', 'update', 'destroy']);
        Route::resource('premium', PremiumController::class)->except(['create', 'edit']);
        Route::resource('bonuses', BonusController::class)->except(['create', 'edit']);
        Route::post('query-bonuses', [BonusController::class, 'consultaPrima']);
        Route::get('check-bonuses/{period}', [BonusController::class, 'checkBonuses']);
        Route::get('bonuses-report/{anio}/{period}/{pagado}', [BonusController::class, 'reportBonus']);
        Route::get('bonus-stubs/{anio}/{period}', [BonusController::class, 'pdfGenerate']);
        Route::get('bonus-stub/{id}/{period}', [BonusPersonController::class, 'pdfGenerate']);

        Route::get('/dotations-type',  [DotationController::class, 'getDotationType']);
        Route::get('measure-active', [MeasureController::class, 'measureActive']);

        /* Paginations */
        Route::get('paginateBodegas', [BodegasController::class,'paginate']);
        Route::get('loan-paginate', [LoanController::class, 'paginate']);
        Route::get('paginateTravel-expense-estimation', [TravelExpenseEstimationController::class,'paginate']);
        Route::get('paginateTravelExpenseEstimationValue', [TravelExpenseEstimationValuesController::class,'paginate']);
        Route::get('paginateThickness', [ThicknessController::class, 'paginate']);
        Route::get('paginate-work-certificate', [WorkCertificateController::class, 'paginate']);
        Route::get('paginate-layoffs-certificate', [LayoffsCertificateController::class, 'paginate']);
        Route::get('get-rotating-turns', [RotatingTurnController::class, 'paginate']);
        Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);
        Route::get('citiesCountry/{idCountry}', [CityController::class, 'getCitiesCountry']);
        Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);
        Route::get('paginateQuotations', [QuotationController::class, 'paginate']);
        Route::get('paginateMunicipality', [MunicipalityController::class, 'paginate']);
        Route::get('paginateContractType', [WorkContractTypeController::class, 'paginate']);
        Route::get('paginateSalaryType', [SalaryTypesController::class, 'paginate']);
        Route::get('paginateDocumentType', [DocumentTypesController::class, 'paginate']);
        Route::get('paginate-fixed-turns', [FixedTurnController::class, 'paginate']);
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
        Route::get('paginate-vacation', [PayVacationController::class, 'paginate']);
        Route::get('paginateBankAccount', [BankAccountsController::class, 'paginate']);
        Route::get('paginateProfessions', [ProfessionController::class, 'paginate']);
        Route::get('paginateFixedAssetType', [FixedAssetTypeController::class, 'paginate']);
        Route::get('paginateRetentionType', [RetentionTypeController::class, 'paginate']);
        Route::get('paginateRegimeType', [RegimeTypeController::class, 'paginate']);
        Route::get('paginateFiscalResponsibility', [FiscalResponsibilityController::class, 'paginate']);
        Route::get('paginateHotels', [HotelController::class, 'paginate']);
        Route::get('paginateTaxis', [TaxiControlller::class, 'paginate']);
        Route::get('paginateCities', [CityController::class, 'paginate']);
        Route::get('paginateDrivingLicences', [DrivingLicenseController::class, 'paginate']);
        Route::get('paginateVisaTypes', [VisaTypeController::class, 'paginate']);
        Route::get('paginateMaterial', [MaterialController::class, 'paginate']);
        Route::get('paginateGeometry', [GeometryController::class, 'paginate']);
        Route::get('paginateUnits', [UnitController::class, 'paginate']);
        Route::get('paginateMachines', [MachineToolController::class, 'paginate']);
        Route::get('paginateInternalProcesses', [InternalProcessController::class, 'paginate']);
        Route::get('paginateExternalProcesses', [ExternalProcessController::class, 'paginate']);
        Route::get('paginateMeasure', [MeasureController::class, 'paginate']);
        Route::get('paginateIndirectCost', [IndirectCostController::class, 'paginate']);
        Route::get('paginateCutLaserMaterial', [CutLaserMaterialController::class, 'paginate']);
        Route::get('paginateAlert', [AlertController::class, 'paginate']);
        Route::get('read-alert', [AlertController::class, 'read']);
        Route::get('budgets-paginate', [BudgetController::class, 'paginate']);
        Route::get('paginationApuProfiles', [ApuProfileController::class, 'paginate']);
        Route::get('paginationApuServices', [ApuServiceController::class, 'paginate']);
        Route::get('paginateApus', [ApuController::class, 'paginate']);
        Route::get('paginateLunchValue', [LunchValueController::class, 'paginate']);
        Route::get('paginate-contract-term', [ContractTermController::class, 'paginate']);
        Route::get('paginate-locations', [LocationController::class, 'paginate']);
        Route::get('paginate-bonuses', [BonusController::class, 'paginate']);
        /* Paginations */

        Route::get('person/{id}', [PersonController::class, 'basicData']);
        Route::get('basicData/{id}', [PersonController::class, 'basicDataForm']);
        Route::post('updatebasicData/{id}', [PersonController::class, 'updateBasicData']);
        Route::get('salary/{id}', [PersonController::class, 'salary']);
        Route::get('salary-history/{id}', [PersonController::class, 'salaryHistory']);
        Route::post('salary', [PersonController::class, 'updateSalaryInfo']);
        Route::get('afiliation/{id}', [PersonController::class, 'afiliation']);
        Route::post('updateAfiliation/{id}', [PersonController::class, 'updateAfiliation']);
        Route::get('epss', [PersonController::class, 'epss']);
        Route::get('fixed_turn', [PersonController::class, 'fixed_turn']);
        Route::post('enterpriseData', [WorkContractController::class, 'updateEnterpriseData']);
        Route::post('finish-contract', [WorkContractController::class, 'finishContract']);
        Route::get('countable_income', [BonificationsController::class, 'countable_income']);
        Route::get('contractsToExpire', [WorkContractController::class, 'contractsToExpire']);
        Route::get('contractRenewal/{id}', [WorkContractController::class, 'contractRenewal']);
        Route::get('preLiquidado', [WorkContractController::class, 'getPreliquidated']);
        Route::get('liquidado/{id}', [WorkContractController::class, 'getLiquidated']);
        Route::get('periodoP', [WorkContractController::class, 'getTrialPeriod']);
        Route::get('memorandums', [MemorandumController::class, 'getMemorandum']);
        Route::get('ListLimitated', [memorandumTypeController::class, 'getListLimitated']);
        Route::get('process/{id}', [DisciplinaryProcessController::class, 'process']);
        Route::put('process/{processId}', [DisciplinaryProcessController::class, 'update']);
        Route::get('cities-by-municipalities/{id}', [CityController::class, 'showByMunicipality']);
        Route::get('countries-with-departments', [CountryController::class, 'allCountries']);
        // !sugerencia Route::get('processByPerson/{id}', [DisciplinaryProcessController::class, 'process']);

        /** Rutas de Empresas  */
        Route::get('companyData', [CompanyController::class, 'getBasicData']);
        Route::get('companyAll', [CompanyController::class, 'getAllCompanies']);
        Route::get('companyData/{id}', [CompanyController::class, 'getBasicDataForId']);
        Route::post('saveCompanyData', [CompanyController::class, 'saveCompanyData']);
        Route::get('/company-global', [CompanyController::class, 'getGlobal']);

        Route::resource("subcategory", SubcategoryController::class)->only(['index', 'store', 'show', 'update']);
        Route::post("subcategory-variable/{id}", [SubcategoryController::class, 'deleteVariable']);

        //boards
        Route::get("board", [BoardController::class, "getData"]);
        Route::post('person/set-board/{personId}/{board}', [BoardController::class, 'setBoardsPerson']);
        Route::get('person/get-boards/{personId}', [BoardController::class, 'personBoards']);

        //tareas
        Route::get('taskview/{id}', [TaskController::class, 'taskView']);
        Route::post('newtask', [TaskController::class, 'new']);
        Route::post('newcomment', [TaskController::class, 'newComment']);
        Route::get('deletecomment/{id}', [TaskController::class, 'deleteComment']);
        Route::get('taskperson/{personId}', [TaskController::class, 'person']);
        Route::get('taskfor/{id}', [TaskController::class, 'getAsignadas']);
        Route::get('person-tasks', [TaskController::class, 'personTasks']);
        Route::post('status-update', [TaskController::class, 'statusUpdate']);
        Route::get('update-comments', [TaskController::class, 'updateComments']);
        Route::get('get-archivadas', [TaskController::class, 'getArchivadas']);
        Route::resource('task-types', TaskTypeController::class);
        Route::get('paginate-task-types', [TaskTypeController::class, 'paginate']);

        //se ejecuta al crear
        Route::get("subcategory-field/{id}", [SubcategoryController::class, 'getField']);

        //se ejecuta al editar
        Route::get("subcategory-edit/{id?}/{idSubcategoria}", [SubcategoryController::class, 'getFieldEdit']);
        Route::resource("product", ProductController::class)->only(['index', 'store', 'update']);
        Route::resource("type-documents", DocumentTypesController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource("category", CategoryController::class);

        //Route::get('add-thirds-params', [ThirdPartyController::class, 'loanpdf']);
        Route::get('proyeccion_pdf/{id}', [LoanController::class, 'loanpdf']);
        // Route::post('attentionCall', [MemorandumController::class, 'attentionCall']);
        Route::post('approve/{id}', [TravelExpenseController::class, 'approve']);
        Route::get('all-zones', [ZonesController::class, 'allZones']);
        Route::get('all-municipalities', [MunicipalityController::class, 'allMunicipalities']);
        Route::get('municipalities-for-dep/{id}', [MunicipalityController::class, 'municipalitiesForDep']);
        Route::get('account-plan', [AccountPlanController::class, 'accountPlan']); //!Se debería usar la que está en php
        Route::get('third-parties-list', [ThirdPartyController::class, 'thirdParties']);
        Route::put('state-change', [LunchController::class, 'activateOrInactivate']);
        Route::get('filter-all-depencencies', [DependencyController::class, 'dependencies']);
        Route::get('filter-all-positions', [PositionController::class, 'positions']);
        Route::get('alert/{id}', [AttentionCallController::class, 'callAlert']);
        Route::get('descargo/{id}', [DisciplinaryProcessController::class, 'descargoPdf']);
        Route::get('download-work-contracts/{id}', [WorkContractController::class, 'pdf']);
        Route::get('get-turn-types', [WorkContractController::class, 'getTurnTypes']);
        Route::put('activate-inactivate', [ThirdPartyController::class, 'changeState']);
        Route::get('fields-third', [ThirdPartyController::class, 'getFields']);
        Route::put('liquidateOrActivate/{id}', [PersonController::class, 'liquidateOrActivate']);
        Route::get('users/{id}', [PersonController::class, 'user']);
        Route::put('blockOrActivate/{id}', [PersonController::class, 'blockOrActivateUser']);
        Route::get('thirdPartyClient', [ThirdPartyController::class, 'thirdPartyClient']);
        Route::get('peopleSelects', [PersonController::class, 'peopleSelects']); //mismo servicio que people->index pero hasta 100 registros
        Route::put('act-inact-medidas', [MeasureController::class, 'changeState']);
        /****** Rutas del modulo APU PIEZA ******/
        Route::put('apu-part-activate-Inactive', [ApuPartController::class, 'activateOrInactivate']);
        Route::get('apu-pieza/pdf/{id}', [ApuPartController::class, 'pdf']);
        Route::get('material-thickness', [MaterialController::class, 'getMaterialThickness']);
        /****** End Rutas del modulo APU PIEZA ******/

        /** Liquidacion Funcionarios */
        Route::get('nomina/liquidaciones/funcionarios/{id}/mostrar/{fechaFin?}', [LiquidacionesController::class, 'get']);
        Route::post('nomina/liquidaciones/{id}/vacaciones_actuales', [LiquidacionesController::class, 'getWithVacacionesActuales']);
        Route::post('nomina/liquidaciones/{id}/salario_base', [LiquidacionesController::class, 'getWithSalarioBase']);
        Route::post('nomina/liquidaciones/{id}/bases', [LiquidacionesController::class, 'getWithBases']);
        Route::post('nomina/liquidaciones/{id}/ingresos', [LiquidacionesController::class, 'getWithIngresos']);
        Route::post('nomina/liquidaciones/previsualizacion', [LiquidacionesController::class, 'getPdfLiquidacion']);
        Route::get('nomina/liquidaciones/dias-trabajados/{id}/{fechaFin}', [LiquidacionesController::class, 'getDiasTrabajados']);
        Route::resource('liquidation', LiquidationsController::class)->only(['index', 'store', 'show']);


        /****** Rutas del modulo APU CONJUNTO ******/
        Route::put('apu-set-activate-Inactive', [ApuSetController::class, 'activateOrInactivate']);
        Route::get('apu-set/pdf/{id}', [ApuSetController::class, 'pdf']);
        Route::get('apu-parts-list', [ApuSetController::class, 'apuParts']);
        Route::get('apu-parts-find', [ApuPartController::class, 'find']);

        Route::get('apu-sets-list', [ApuSetController::class, 'apuSets']);
        Route::get('apu-sets-find', [ApuSetController::class, 'find']);
        Route::post('calculation-bases-update', [CalculationBaseController::class, 'updateAll']);
        /****** End Rutas del modulo APU CONJUNTO ******/

        Route::get('budgets-download-client', [BudgetController::class, 'downloadClient']);
        /****** Rutas del modulo APU Servicio ******/
        Route::get('activateOrInactApuService', [ApuServiceController::class, 'activateOrInactivate']);
        /****** End Rutas del modulo APU Servicio ******/
        Route::get('lunches/download/{inicio?}/{fin?}', [LunchController::class, 'download'])->where([
            'inicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
            'fin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
        ]);
        Route::get('legal_document/{disciplinary_process_id}', [DisciplinaryProcessController::class, 'legalDocument']);
        Route::post('legal_document', [DisciplinaryProcessController::class, 'saveLegalDocument']);
        Route::put('legal_document/{id}', [DisciplinaryProcessController::class, 'InactiveDOcument']);
        Route::post('approve_process/{disciplinary_process_id}', [DisciplinaryProcessController::class, 'approve']);
        Route::post('new-business-budget', [BusinessController::class, 'newBusinessBudget']);
        Route::post('save-task', [BudgetController::class, 'saveTask']);
        Route::get('get-tasks-business/{id}', [BusinessController::class, 'getTasks']);

        /************RUTAS PHP************/
        Route::get('php/categoria_nueva/detalle_categoria_nueva_general.php', [CategoriaNuevaController::class, 'index']);
        Route::get('php/genericos/departamentos.php', [CategoriaNuevaController::class, 'getDepartamentos']);
        Route::get('php/categoria_nueva/detalle_categoria_nueva_departamento.php', [CategoriaNuevaController::class, 'categoriaDepartamento']);
        Route::get('php/comprasnacionales/lista_compras', [ListaComprasController::class, 'index']);
        Route::get('php/rotativoscompras/lista_pre_compra', [ListaComprasController::class, 'preCompras']);
        Route::get('php/funcionarios/lista_funcionarios', [ListaComprasController::class, 'getFuncionarios']);
    }
);
