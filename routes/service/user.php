<?php

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

Route::group(['middleware' => 'auth:user'], function () {
    //For category service
    Route::post('/init/request', 'V1\Service\User\ServiceController@add_cancel_request');
    Route::get('/zipcode/provider', 'V1\Service\User\HomeController@zipcodeprovider');
    Route::get('/service_category', 'V1\Service\User\HomeController@service_category');
    Route::get('/service_sub_category/{id}', 'V1\Service\User\HomeController@service_sub_category');
    Route::get('/service_project_category/{id}', 'V1\Service\User\HomeController@projectcategories');
    Route::get('/service_main_service/{id}', 'V1\Service\User\HomeController@main_services');
    Route::get('/services/{id}/{ids}', 'V1\Service\User\HomeController@service');
    Route::get('/service_city_price/{id}', 'V1\Service\User\HomeController@service_city_price');
    Route::get('/zipcode/list', 'V1\Service\User\ServiceController@providerServiceListZipCode');
    Route::get('/list', 'V1\Service\User\ServiceController@providerServiceList');
    Route::get('/review/{id}', 'V1\Service\User\ServiceController@review');
    Route::get('/servicelist/{id}', 'V1\Service\User\ServiceController@service');
    Route::post('/cancelrequest/{id}', 'V1\Service\User\ServiceController@cancel_request');
    Route::post('service/send/request', 'V1\Service\User\ServiceController@create_service');
    Route::get('/service/check/request', 'V1\Service\User\ServiceController@status');
    Route::get('/service/request/{id}', 'V1\Service\User\ServiceController@checkService');
    Route::post('/service/cancel/request', 'V1\Service\User\ServiceController@cancel_service');
    Route::post('/service/rate', 'V1\Service\User\ServiceController@rate');
    Route::post('/service/payment', 'V1\Service\User\ServiceController@payment');
    Route::post('/service/update/payment', 'V1\Service\User\ServiceController@update_payment_method');
    Route::get('/promocode', 'V1\Service\User\ServiceController@promocode');
    Route::post('/update/service/{id}', 'V1\Service\User\ServiceController@update_service');
    //History details
    Route::get('/request/details', 'V1\Service\User\HomeController@request_details');
    Route::get('/trips-history/service', 'V1\Service\User\HomeController@trips');
    Route::get('/trips-history/service/{id}', 'V1\Service\User\HomeController@gettripdetails');
    Route::get('/services/dispute', 'V1\Service\User\HomeController@getdisputedetails');
    Route::post('/service/dispute', 'V1\Service\User\HomeController@service_request_dispute');
    Route::get('/dispute/service', 'V1\Service\User\HomeController@getUserdisputedetails');
    Route::get('/service/disputestatus/{id}', 'V1\Service\User\HomeController@get_service_request_dispute');
    Route::post('/service/dispute', 'V1\Service\User\HomeController@service_request_dispute');
});
