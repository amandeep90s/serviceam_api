<?php

use App\Http\Controllers\Common\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Common\Admin\Resource\DocumentController;
use App\Http\Controllers\Common\Admin\Resource\ProviderController;
use App\Http\Controllers\Common\Admin\Resource\UserController;
use App\Http\Controllers\Common\CommonController;
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
    Route::get('/notification', 'V1\Common\Admin\Resource\NotificationController@index');
    Route::post('/notification', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\NotificationController@store']);
    Route::get('/notification/{id}', 'V1\Common\Admin\Resource\NotificationController@show');
    Route::patch('/notification/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\NotificationController@update']);
    Route::delete('/notification/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\NotificationController@destroy']);
    Route::get('/notification/days/list', 'V1\Common\Admin\Resource\NotificationController@daysindex');
    Route::post('/notification/days', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\NotificationController@daysstore']);
    Route::get('/notification/days/{id}', 'V1\Common\Admin\Resource\NotificationController@daysshow');
    Route::patch('/notification/days/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\NotificationController@daysupdate']);
    //Reason
    Route::get('/reason', 'V1\Common\Admin\Resource\ReasonController@index');
    Route::post('/reason', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ReasonController@store']);
    Route::get('/reason/{id}', 'V1\Common\Admin\Resource\ReasonController@show');
    Route::patch('/reason/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ReasonController@update']);
    Route::delete('/reason/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ReasonController@destroy']);
    //Fleet
    Route::get('/fleet', 'V1\Common\Admin\Resource\FleetController@index');
    Route::post('/fleet', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\FleetController@store']);
    Route::get('/fleet/{id}', 'V1\Common\Admin\Resource\FleetController@show');
    Route::patch('/fleet/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\FleetController@update']);
    Route::delete('/fleet/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\FleetController@destroy']);
    Route::get('/fleet/{id}/updateStatus', 'V1\Common\Admin\Resource\FleetController@updateStatus');
    Route::post('/card', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\FleetController@addcard']);
    Route::get('card', 'V1\Common\Admin\Resource\FleetController@card');
    Route::post('add/money', 'V1\Common\Admin\Resource\FleetController@wallet');
    // Route::get('wallet', 'V1\Common\Admin\Resource\FleetController@wallet');
    Route::get('adminfleet/wallet', 'V1\Common\Admin\Resource\FleetController@wallet');
    //Dispatcher Panel
    Route::get('/dispatcher/trips', 'V1\Common\Admin\Resource\DispatcherController@trips');
    Route::get('/list', 'V1\Common\Admin\Resource\DispatcherController@providerServiceList');
    //Dispatcher
    Route::get('/dispatcher', 'V1\Common\Admin\Resource\DispatcherController@index');
    Route::post('/dispatcher', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DispatcherController@store']);
    Route::get('/dispatcher/{id}', 'V1\Common\Admin\Resource\DispatcherController@show');
    Route::patch('/dispatcher/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DispatcherController@update']);
    Route::delete('/dispatcher/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DispatcherController@destroy']);
    Route::get('/dispatcher/get/providers', 'V1\Common\Admin\Resource\DispatcherController@providers');
    Route::post('/dispatcher/assign', 'V1\Common\Admin\Resource\DispatcherController@assign');
    Route::post('/dispatcher/ride/request', 'V1\Common\Admin\Resource\DispatcherController@create_ride');
    Route::post('/dispatcher/ride/cancel', 'V1\Common\Admin\Resource\DispatcherController@cancel_ride');
    Route::post('/dispatcher/service/request', 'V1\Common\Admin\Resource\DispatcherController@create_service');
    Route::post('/dispatcher/service/cancel', 'V1\Common\Admin\Resource\DispatcherController@cancel_service');
    Route::get('/fare', 'V1\Common\Admin\Resource\DispatcherController@fare');
    //Account Manager
    Route::get('/accountmanager', 'V1\Common\Admin\Resource\AccountManagerController@index');
    Route::post('/accountmanager', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AccountManagerController@store']);
    Route::get('/accountmanager/{id}', 'V1\Common\Admin\Resource\AccountManagerController@show');
    Route::patch('/accountmanager/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AccountManagerController@update']);
    Route::delete('/accountmanager/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AccountManagerController@destroy']);
    //Promocodes
    Route::get('/promocode', 'V1\Common\Admin\Resource\PromocodeController@index');
    Route::post('/promocode', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PromocodeController@store']);
    Route::get('/promocode/{id}', 'V1\Common\Admin\Resource\PromocodeController@show');
    Route::patch('/promocode/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PromocodeController@update']);
    Route::delete('/promocode/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PromocodeController@destroy']);
    //Geofencing
    Route::get('/geofence', 'V1\Common\Admin\Resource\GeofenceController@index');
    Route::post('/geofence', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\GeofenceController@store']);
    Route::get('/geofence/{id}', 'V1\Common\Admin\Resource\GeofenceController@show');
    Route::patch('/geofence/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\GeofenceController@update']);
    Route::get('/geofence/{id}/updateStatus', 'V1\Common\Admin\Resource\GeofenceController@updateStatus');
    Route::delete('/geofence/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\GeofenceController@destroy']);
    //Dispute
    Route::get('/dispute_list', 'V1\Common\Admin\Resource\DisputeController@index');
    Route::post('/dispute', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DisputeController@store']);
    Route::get('/dispute/{id}', 'V1\Common\Admin\Resource\DisputeController@show');
    Route::patch('/dispute/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DisputeController@update']);
    Route::delete('/dispute/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\DisputeController@destroy']);
    //Provider
    Route::get('/provider', 'V1\Common\Admin\Resource\ProviderController@index');
    Route::post('/provider', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ProviderController@store']);
    Route::get('/provider/{id}', 'V1\Common\Admin\Resource\ProviderController@show');
    Route::patch('/provider/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ProviderController@update']);
    Route::delete('/provider/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ProviderController@destroy']);
    Route::get('/provider/{id}/updateStatus', 'V1\Common\Admin\Resource\ProviderController@updateStatus');
    Route::get('/provider/approve/{id}', 'V1\Common\Admin\Resource\ProviderController@approveStatus');
    Route::get('/provider/zoneapprove/{id}', 'V1\Common\Admin\Resource\ProviderController@zoneStatus');
    Route::post('/provider/addamount/{id}', ['uses' => 'V1\Common\Admin\Resource\ProviderController@addamount', 'middleware' => ['permission:provider-status']]);
    //sub admin
    Route::get('/subadminlist/{type}', 'V1\Common\Admin\Resource\AdminController@index');
    Route::post('/subadmin', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AdminController@store']);
    Route::get('/subadmin/{id}', 'V1\Common\Admin\Resource\AdminController@show');
    Route::patch('/subadmin/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AdminController@update']);
    Route::delete('/subadmin/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\AdminController@destroy']);
    Route::get('/subadmin/{id}/updateStatus', 'V1\Common\Admin\Resource\AdminController@updateStatus');
    Route::get('/heatmap', 'V1\Common\Admin\Resource\AdminController@heatmap');
    Route::get('/role_list', 'V1\Common\Admin\Resource\AdminController@role_list');
    //cmspages
    Route::get('/cmspage', 'V1\Common\Admin\Resource\CmsController@index');
    Route::post('/cmspage', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CmsController@store']);
    Route::get('/cmspage/{id}', 'V1\Common\Admin\Resource\CmsController@show');
    Route::patch('/cmspage/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CmsController@update']);
    Route::delete('/cmspage/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CmsController@destroy']);
    //custom push
    Route::get('/custompush', 'V1\Common\Admin\Resource\CustomPushController@index');
    Route::post('/custompush', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CustomPushController@store']);
    Route::get('/custompush/{id}', 'V1\Common\Admin\Resource\CustomPushController@show');
    Route::patch('/custompush/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CustomPushController@update']);
    Route::delete('/custompush/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CustomPushController@destroy']);
    //Provider add vehicle
    Route::get('/ProviderService/{id}', 'V1\Common\Admin\Resource\ProviderController@ProviderService');
    Route::patch('/vehicle_type', 'V1\Common\Admin\Resource\ProviderController@vehicle_type');
    Route::get('/service_on/{id}', 'V1\Common\Admin\Resource\ProviderController@service_on');
    Route::get('/service_off/{id}', 'V1\Common\Admin\Resource\ProviderController@service_off');
    Route::get('/deleteservice/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ProviderController@deleteservice']);
    //Provider view document
    Route::get('/provider/{id}/view_document', 'V1\Common\Admin\Resource\ProviderController@view_document');
    Route::get('/provider/approve_image/{id}', 'V1\Common\Admin\Resource\ProviderController@approve_image');
    Route::get('/provider/approveall/{id}', 'V1\Common\Admin\Resource\ProviderController@approve_all');
    Route::delete('/provider/delete_view_image/{id}', 'V1\Common\Admin\Resource\ProviderController@delete_view_image');
    //CompanyCountry
    Route::get('/providerdocument/{id}', 'V1\Common\Admin\Resource\ProviderController@providerdocument');
    Route::get('/companycountries', 'V1\Common\Admin\Resource\CompanyCountriesController@index');
    Route::post('/companycountries', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCountriesController@store']);
    Route::get('/companycountries/{id}', 'V1\Common\Admin\Resource\CompanyCountriesController@show');
    Route::patch('/companycountries/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCountriesController@update']);
    Route::delete('/companycountries/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCountriesController@destroy']);
    Route::get('/companycountries/{id}/updateStatus', 'V1\Common\Admin\Resource\CompanyCountriesController@updateStatus');
    Route::get('/companycountries/{id}/bankform', 'V1\Common\Admin\Resource\CompanyCountriesController@getBankForm');
    Route::post('/bankform', 'V1\Common\Admin\Resource\CompanyCountriesController@storeBankform');
    //country list
    Route::get('/countries', 'V1\Common\Admin\Resource\CompanyCountriesController@countries');
    Route::get('/states/{id}', 'V1\Common\Admin\Resource\CompanyCountriesController@states');
    Route::get('/cities/{id}', 'V1\Common\Admin\Resource\CompanyCountriesController@cities');
    Route::get('/company_country_list', 'V1\Common\Admin\Resource\CompanyCountriesController@companyCountries');
    Route::get('/vehicle_type_list', 'V1\Transport\Admin\VehicleController@vehicletype');
    //Route::get('/gettaxiprice/{id}', 'V1\Transport\Admin\VehicleController@gettaxiprice');
    //CompanyCity
    Route::get('/companycityservice', 'V1\Common\Admin\Resource\CompanyCitiesController@index');
    Route::post('/companycityservice', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCitiesController@store']);
    Route::get('/companycityservice/{id}', 'V1\Common\Admin\Resource\CompanyCitiesController@show');
    Route::patch('/companycityservice/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCitiesController@update']);
    Route::delete('/companycityservice/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\CompanyCitiesController@destroy']);
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
    //Roles
    Route::get('/roles', 'V1\Common\Admin\Resource\RolesController@index');
    Route::post('/roles', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\RolesController@store']);
    Route::get('/roles/{id}', 'V1\Common\Admin\Resource\RolesController@show');
    Route::patch('/roles/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\RolesController@update']);
    Route::delete('/roles/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\RolesController@destroy']);
    Route::get('/permission', 'V1\Common\Admin\Resource\RolesController@permission');
    //peakhours
    Route::get('/peakhour', 'V1\Common\Admin\Resource\PeakHourController@index');
    Route::post('/peakhour', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PeakHourController@store']);
    Route::get('/peakhour/{id}', 'V1\Common\Admin\Resource\PeakHourController@show');
    Route::patch('/peakhour/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PeakHourController@update']);
    Route::delete('/peakhour/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PeakHourController@destroy']);
    // ratings
    Route::get('/userreview', 'V1\Common\Admin\Resource\AdminController@userReview');
    Route::get('/providerreview', 'V1\Common\Admin\Resource\AdminController@providerReview');
    //Menu
    Route::get('/menu', 'V1\Common\Admin\Resource\MenuController@index');
    Route::post('/menu', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\MenuController@store']);
    Route::get('/menu/{id}', 'V1\Common\Admin\Resource\MenuController@show');
    Route::patch('/menu/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\MenuController@update']);
    Route::delete('/menu/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\MenuController@destroy']);
    Route::patch('/menucity/{id}', 'V1\Common\Admin\Resource\MenuController@menucity');
    Route::get('/ride_type', 'V1\Common\Admin\Resource\MenuController@ride_type');
    Route::get('/service_type', 'V1\Common\Admin\Resource\MenuController@service_type');
    Route::get('/order_type', 'V1\Common\Admin\Resource\MenuController@order_type');
    // Route::get('/getcity', 'V1\Common\Admin\Resource\MenuController@getcity');
    Route::get('/getCountryCity/{serviceId}/{CountryId}', 'V1\Common\Admin\Resource\MenuController@getCountryCity');
    Route::get('/getmenucity/{id}', 'V1\Common\Admin\Resource\MenuController@getmenucity');
    //payrolls
    Route::get('/zone', 'V1\Common\Admin\Resource\ZoneController@index');
    Route::post('/zone', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ZoneController@store']);
    Route::get('/zone/{id}', 'V1\Common\Admin\Resource\ZoneController@show');
    Route::patch('/zone/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ZoneController@update']);
    Route::delete('/zone/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\ZoneController@destroy']);
    Route::get('/zones/{id}/updateStatus', 'V1\Common\Admin\Resource\ZoneController@updateStatus');
    Route::get('/payroll-template', 'V1\Common\Admin\Resource\PayrollTemplateController@index');
    Route::post('/payroll-template', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollTemplateController@store']);
    Route::get('/payroll-template/{id}', 'V1\Common\Admin\Resource\PayrollTemplateController@show');
    Route::patch('/payroll-template/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollTemplateController@update']);
    Route::delete('/payroll-template/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollTemplateController@destroy']);
    Route::get('/payroll-templates/{id}/updateStatus', 'V1\Common\Admin\Resource\PayrollTemplateController@updateStatus');
    Route::get('/payroll', 'V1\Common\Admin\Resource\PayrollController@index');
    Route::post('/payroll', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollController@store']);
    Route::get('/payroll/{id}', 'V1\Common\Admin\Resource\PayrollController@show');
    Route::patch('/payroll/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollController@update']);
    Route::delete('/payroll/{id}', ['middleware' => 'demo', 'uses' => 'V1\Common\Admin\Resource\PayrollController@destroy']);
    Route::get('/payrolls/{id}/updateStatus', 'V1\Common\Admin\Resource\PayrollController@updateStatus');
    Route::post('/payroll/update-payroll', 'V1\Common\Admin\Resource\PayrollController@updatePayroll');
    Route::get('/zoneprovider/{id}', 'V1\Common\Admin\Resource\PayrollController@zoneprovider');
    Route::get('/payrolls/download/{id}', 'V1\Common\Admin\Resource\PayrollController@PayrollDownload');
    Route::get('/cityzones/{id}', 'V1\Common\Admin\Resource\ZoneController@cityzones');
    Route::get('/zonetype/{id}', 'V1\Common\Admin\Resource\ZoneController@cityzonestype');
    Route::get('bankdetails/template', 'V1\Common\Provider\HomeController@template');
    Route::post('/addbankdetails', 'V1\Common\Provider\HomeController@addbankdetails');
    Route::post('/editbankdetails', 'V1\Common\Provider\HomeController@editbankdetails');
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
