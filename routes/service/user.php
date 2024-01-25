<?php

use App\Http\Controllers\Service\User\HomeController as UserHomeController;
use App\Http\Controllers\Service\User\ServiceController as UserServiceController;
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

Route::group(['middleware' => 'auth:user'], function () {
    // USER SERVICE ROUTES
    Route::get('/zipcode/provider', [UserHomeController::class, 'zipcodeprovider']);
    Route::get('/service_category', [UserHomeController::class, 'service_category']);
    Route::get('/service_sub_category/{id}', [UserHomeController::class, 'service_sub_category']);
    Route::get('/service_project_category/{id}', [UserHomeController::class, 'projectcategories']);
    Route::get('/service_main_service/{id}', [UserHomeController::class, 'main_services']);
    Route::get('/services/{id}/{ids}', [UserHomeController::class, 'service']);
    Route::get('/service_city_price/{id}', [UserHomeController::class, 'service_city_price']);

    // History details
    Route::get('/request/details', [UserHomeController::class, 'request_details']);
    Route::get('/trips-history/service', [UserHomeController::class, 'trips']);
    Route::get('/trips-history/service/{id}', [UserHomeController::class, 'gettripdetails']);
    Route::get('/services/dispute', [UserHomeController::class, 'getdisputedetails']);
    Route::post('/service/dispute', [UserHomeController::class, 'service_request_dispute']);
    Route::get('/dispute/service', [UserHomeController::class, 'getUserdisputedetails']);
    Route::get('/service/disputestatus/{id}', [UserHomeController::class, 'get_service_request_dispute']);

    // USER SERVICE ROUTES
    Route::post('/init/request', [UserServiceController::class, 'add_cancel_request']);
    Route::get('/zipcode/list', [UserServiceController::class, 'providerServiceListZipCode']);
    Route::get('/list', [UserServiceController::class, 'providerServiceList']);
    Route::get('/review/{id}', [UserServiceController::class, 'review']);
    Route::get('/servicelist/{id}', [UserServiceController::class, 'service']);
    Route::post('/cancelrequest/{id}', [UserServiceController::class, 'cancel_request']);
    Route::post('service/send/request', [UserServiceController::class, 'create_service']);
    Route::get('/service/check/request', [UserServiceController::class, 'status']);
    Route::get('/service/request/{id}', [UserServiceController::class, 'checkService']);
    Route::post('/service/cancel/request', [UserServiceController::class, 'cancel_service']);
    Route::post('/service/rate', [UserServiceController::class, 'rate']);
    Route::post('/service/payment', [UserServiceController::class, 'payment']);
    Route::post('/service/update/payment', [UserServiceController::class, 'update_payment_method']);
    Route::get('/promocode', [UserServiceController::class, 'promocode']);
    Route::post('/update/service/{id}', [UserServiceController::class, 'update_service']);
});
