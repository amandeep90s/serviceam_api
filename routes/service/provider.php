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

Route::group(['middleware' => 'auth:provider'], function () {
    Route::get('/providerservice/categories', 'V1\Service\Provider\HomeController@categories');
    Route::post('/providerservice/subcategories', 'V1\Service\Provider\HomeController@subcategories');
    Route::post('/providerservice/service', 'V1\Service\Provider\HomeController@service');
    Route::get('/totalservices', 'V1\Service\Provider\HomeController@totalservices');
    Route::get('/listtotalservices', 'V1\Service\Provider\HomeController@listtotalservices');
    Route::get('/check/serve/request', 'V1\Service\Provider\ServeController@index');
    Route::post('/update/serve/request', 'V1\Service\Provider\ServeController@updateServe');
    Route::patch('/update/serve/request', 'V1\Service\Provider\ServeController@updateServe');
    Route::post('/cancel/serve/request', 'V1\Service\Provider\ServeController@cancelServe');
    Route::post('/rate/serve', 'V1\Service\Provider\ServeController@rate');
    Route::get('/history/{type}/service', 'V1\Service\Provider\ServeController@historyList');
    Route::get('/history/service/{id}', 'V1\Service\Provider\ServeController@getServiceHistorydetails');
    Route::get('/service/disputestatus/{id}', 'V1\Service\Provider\ServeController@getServiceRequestDispute');
    Route::post('/history-dispute/service', 'V1\Service\Provider\ServeController@saveServiceRequestDispute');
    Route::get('/services/dispute', 'V1\Service\Provider\ServeController@getdisputedetails');
    Route::get('/dispute/service', 'V1\Service\Provider\ServeController@getdisputedetails');
    //PROJECT CATEGORIES
    Route::get('/service/fare_type-list', 'V1\Service\Provider\ServeController@fareTypeList');
    Route::get('/service/categories-list', 'V1\Service\Provider\ServeController@categoriesList');
    Route::get('/service/subcategories-list/{id}', 'V1\Service\Provider\ServeController@subCategoriesList');
    Route::get('/service/servicelist/{id}/{subcategoryid}', 'V1\Service\Provider\ServeController@servicesList');
    Route::get('/baseservices', 'V1\Service\Provider\HomeController@fareTypeServiceList');
    Route::post('/add/servicearea', 'V1\Service\User\ServiceController@servicearea');
    Route::get('/servicearea/list', 'V1\Service\User\ServiceController@servicearea_list');
});
