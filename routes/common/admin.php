<?php

use App\Http\Controllers\Common\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Common\Admin\Auth\AdminController;
use App\Http\Controllers\Common\Admin\Resource\AccountManagerController;
use App\Http\Controllers\Common\Admin\Resource\CmsPageController;
use App\Http\Controllers\Common\Admin\Resource\CompanyCityController;
use App\Http\Controllers\Common\Admin\Resource\CompanyCountryController;
use App\Http\Controllers\Common\Admin\Resource\CustomPushController;
use App\Http\Controllers\Common\Admin\Resource\DispatcherController;
use App\Http\Controllers\Common\Admin\Resource\DisputeController;
use App\Http\Controllers\Common\Admin\Resource\DocumentController;
use App\Http\Controllers\Common\Admin\Resource\FleetController;
use App\Http\Controllers\Common\Admin\Resource\GeoFenceController;
use App\Http\Controllers\Common\Admin\Resource\MenuController;
use App\Http\Controllers\Common\Admin\Resource\NotificationController;
use App\Http\Controllers\Common\Admin\Resource\PayrollController;
use App\Http\Controllers\Common\Admin\Resource\PayrollTemplateController;
use App\Http\Controllers\Common\Admin\Resource\PeakHourController;
use App\Http\Controllers\Common\Admin\Resource\PromocodeController;
use App\Http\Controllers\Common\Admin\Resource\ProviderController;
use App\Http\Controllers\Common\Admin\Resource\ReasonController;
use App\Http\Controllers\Common\Admin\Resource\RoleController;
use App\Http\Controllers\Common\Admin\Resource\UserController;
use App\Http\Controllers\Common\Admin\Resource\ZoneController;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\Provider\HomeController as ProviderHomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/refresh', [AdminAuthController::class, 'refresh']);
Route::post('/forgotOtp', [AdminAuthController::class, 'forgotPasswordOTP']);
Route::post('/resetOtp', [AdminAuthController::class, 'resetPasswordOTP']);
Route::group(['middleware' => 'auth:admin'], function () {
    Route::post('/permission_list', [AdminAuthController::class, 'permission_list']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::apiResource('users', UserController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/users/{id}/updateStatus', [UserController::class, 'updateStatus']);

    Route::get('/{type}/logs/{id}', [CommonController::class, 'logdata']);
    Route::get('/{type}/wallet/{id}', [CommonController::class, 'walletDetails']);
    Route::get('/services/main/list', [CommonController::class, 'admin_services']);
    Route::get('/services/list/{id}', [ProviderController::class, 'provider_services']);

    //Document
    Route::apiResource('/document', DocumentController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/document/{id}/updateStatus', [DocumentController::class, 'updateStatus']);

    //Notification
    Route::apiResource('/notification', NotificationController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/notification/days/list', [NotificationController::class, 'daysindex']);
    Route::post('/notification/days', [NotificationController::class, 'daysstore'])->middleware('demo');
    Route::get('//notification/days/{id}', [NotificationController::class, 'daysshow']);
    Route::patch('//notification/days/{id}', [NotificationController::class, 'daysupdate'])->middleware('demo');

    //Reason
    Route::apiResource('/reason', ReasonController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //Fleet
    Route::apiResource('/fleet', FleetController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/fleet/{id}/updateStatus', [FleetController::class, 'updateStatus']);
    Route::post('/card', ['middleware' => 'demo', 'uses' => [FleetController::class, 'addcard']]);
    Route::get('/card', [FleetController::class, 'card']);
    Route::post('/add/money', [FleetController::class, 'wallet']);
    Route::get('/adminfleet/wallet', [FleetController::class, 'wallet']);

    //Dispatcher
    Route::apiResource('/dispatcher', DispatcherController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/dispatcher/get/providers', [DispatcherController::class, 'providers']);
    Route::post('/dispatcher/assign', [DispatcherController::class, 'assign']);
    Route::post('/dispatcher/ride/request', [DispatcherController::class, 'create_ride']);
    Route::post('/dispatcher/ride/cancel', [DispatcherController::class, 'cancel_ride']);
    Route::post('/dispatcher/service/request', [DispatcherController::class, 'create_service']);
    Route::post('/dispatcher/service/cancel', [DispatcherController::class, 'cancel_service']);
    Route::get('/fare', [DispatcherController::class, 'fare']);

    //Dispatcher Panel
    Route::get('/dispatcher/trips', [DispatcherController::class, 'trips']);
    Route::get('/list', [DispatcherController::class, 'providerServiceList']);

    //Account Manager
    Route::apiResource('/accountmanager', AccountManagerController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //Promocodes
    Route::apiResource('/promocode', PromocodeController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //Geofencing
    Route::apiResource('/geofence', GeoFenceController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/geofence/{id}/updateStatus', [GeoFenceController::class, 'updateStatus']);

    //Dispute
    Route::apiResource('/dispute', DisputeController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //Provider
    Route::apiResource('/provider', ProviderController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/provider/{id}/updateStatus', [ProviderController::class, 'updateStatus']);
    Route::get('/provider/approve/{id}', [ProviderController::class, 'approveStatus']);
    Route::get('/provider/zoneapprove/{id}', [ProviderController::class, 'zoneStatus']);
    Route::post('/provider/addamount/{id}', [ProviderController::class, 'addamount'])->middleware(['permission:provider-status']);

    //Provider add vehicle
    Route::get('/ProviderService/{id}', [ProviderController::class, 'ProviderService']);
    Route::patch('/vehicle_type', [ProviderController::class, 'vehicle_type']);
    Route::get('/service_on/{id}', [ProviderController::class, 'service_on']);
    Route::get('/service_off/{id}', [ProviderController::class, 'service_off']);
    Route::get('/deleteservice/{id}', [ProviderController::class, 'deleteservice'])->middleware(['demo']);

    //Provider view document
    Route::get('/provider/{id}/view_document', [ProviderController::class, 'view_document']);
    Route::get('/provider/approve_image/{id}', [ProviderController::class, 'approve_image']);
    Route::get('/provider/approveall/{id}', [ProviderController::class, 'approve_all']);
    Route::delete('/provider/delete_view_image/{id}', [ProviderController::class, 'delete_view_image']);
    Route::get('/providerdocument/{id}', [ProviderController::class, 'providerdocument']);

    //sub admin
    Route::apiResource('/subadmin', AdminController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/subadminlist/{type}', [AdminController::class, 'index']);
    Route::get('/subadmin/{id}/updateStatus', [AdminController::class, 'updateStatus']);
    Route::get('/heatmap', [AdminController::class, 'heatmap']);
    Route::get('/role_list', [AdminController::class, 'role_list']);

    //cmspages
    Route::apiResource('/cmspage', CmsPageController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //custom push
    Route::apiResource('/custompush', CustomPushController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    //CompanyCountry
    Route::apiResource('/companycountries', CompanyCountryController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/companycountries/{id}/updateStatus', [CompanyCountryController::class, 'updateStatus']);
    Route::get('/companycountries/{id}/bankform', [CompanyCountryController::class, 'getBankForm']);
    Route::post('/bankform', [CompanyCountryController::class, 'storeBankform']);

    //country list
    Route::get('/countries', [CompanyCountryController::class, 'countries']);
    Route::get('/states/{id}', [CompanyCountryController::class, 'states']);
    Route::get('/cities/{id}', [CompanyCountryController::class, 'cities']);
    Route::get('/company_country_list', [CompanyCountryController::class, 'companyCountries']);

    // Vehicle routes
    Route::get('/vehicle_type_list', 'V1\Transport\Admin\VehicleController@vehicletype');

    //CompanyCity
    Route::apiResource('/companycityservice', CompanyCityController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/countrycities/{id}', 'V1\Common\Admin\Resource\CompanyCitiesController@countrycities');

    //Account setting details
    Route::get('/profile', 'V1\Common\Admin\Resource\AdminController@show_profile');
    Route::post('/profile', 'V1\Common\Admin\Resource\AdminController@update_profile');
    Route::get('password', 'V1\Common\Admin\Resource\AdminController@password');
    Route::post('password', 'V1\Common\Admin\Resource\AdminController@password_update');
    Route::get('/adminservice', 'V1\Common\Admin\Resource\AdminController@admin_service');
    Route::get('/services/child/list/{id}', 'V1\Common\Admin\Resource\AdminController@child_service');
    Route::get('/heatmap', 'V1\Common\Admin\Resource\AdminController@heatmap');
    Route::get('/godsview', 'V1\Common\Admin\Resource\AdminController@godsview');

    //Admin Seeder
    Route::post('/companyuser', 'V1\Common\Admin\Resource\UserController@companyuser');
    Route::get('/settings', 'V1\Common\Admin\Auth\AdminController@index');
    Route::post('/settings', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Auth\AdminController@settings_store']);

    // Roles
    Route::apiResource('/roles', RoleController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/permission', 'V1\Common\Admin\Resource\RolesController@permission');

    //peakhours
    Route::apiResource('/peakhour', PeakHourController::class)->middleware('demo')->only(['store', 'update', 'destroy']);

    // ratings
    Route::get('/userreview', 'V1\Common\Admin\Resource\AdminController@userReview');
    Route::get('/providerreview', 'V1\Common\Admin\Resource\AdminController@providerReview');

    //Menu
    Route::apiResource('/menu', MenuController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::patch('/menucity/{id}', 'V1\Common\Admin\Resource\MenuController@menucity');
    Route::get('/ride_type', 'V1\Common\Admin\Resource\MenuController@ride_type');
    Route::get('/service_type', 'V1\Common\Admin\Resource\MenuController@service_type');
    Route::get('/order_type', 'V1\Common\Admin\Resource\MenuController@order_type');
    Route::get('/getCountryCity/{serviceId}/{CountryId}', 'V1\Common\Admin\Resource\MenuController@getCountryCity');
    Route::get('/getmenucity/{id}', 'V1\Common\Admin\Resource\MenuController@getmenucity');

    //payrolls
    Route::apiResource('/zone', ZoneController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/zones/{id}/updateStatus', 'V1\Common\Admin\Resource\ZoneController@updateStatus');
    Route::get('/cityzones/{id}', 'V1\Common\Admin\Resource\ZoneController@cityzones');
    Route::get('/zonetype/{id}', 'V1\Common\Admin\Resource\ZoneController@cityzonestype');

    // Payroll Templates
    Route::apiResource('/zone', PayrollTemplateController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/payroll-templates/{id}/updateStatus', 'V1\Common\Admin\Resource\PayrollTemplateController@updateStatus');

    // Payroll
    Route::apiResource('/payroll', PayrollController::class)->middleware('demo')->only(['store', 'update', 'destroy']);
    Route::get('/payrolls/{id}/updateStatus', [PayrollController::class, 'updateStatus']);
    Route::post('/payroll/update-payroll', [PayrollController::class, 'updatePayroll']);
    Route::get('/zoneprovider/{id}', [PayrollController::class, 'zoneprovider']);
    Route::get('/payrolls/download/{id}', [PayrollController::class, 'PayrollDownload']);

    // Provider home controller
    Route::get('/bankdetails/template', [ProviderHomeController::class, 'template']);
    Route::post('/addbankdetails', [ProviderHomeController::class, 'addbankdetails']);
    Route::post('/editbankdetails', [ProviderHomeController::class, 'editbankdetails']);

    Route::get('/provider_total_deatils/{id}', 'V1\Common\Admin\Resource\ProviderController@provider_total_deatils');
    Route::get('/dashboard/{id}', 'V1\Common\Admin\Auth\AdminController@dashboarddata');
    Route::get('/statement/provider', 'V1\Common\Admin\Resource\AllStatementController@statement_provider');
    Route::get('/statement/user', 'V1\Common\Admin\Resource\AllStatementController@statement_user');
    Route::get('/transactions', 'V1\Common\Admin\Resource\AllStatementController@statement_admin');
    //search
    Route::get('/getdata', 'V1\Common\Admin\Resource\AllStatementController@getData');
    Route::get('/getfleetprovider', 'V1\Common\Admin\Resource\AllStatementController@getFleetProvider');
});
Route::get('/payrolls/download/{id}', 'V1\Common\Admin\Resource\PayrollController@PayrollDownload');
Route::get('/searchprovider/{id}', 'V1\Common\Admin\Resource\ProviderController@searchprovider');
