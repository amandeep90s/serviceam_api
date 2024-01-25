<?php

use App\Http\Controllers\Service\Provider\HomeController as ProviderHomeController;
use App\Http\Controllers\Service\Provider\ServiceController as ProviderServiceController;
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

Route::group(['middleware' => 'auth:provider'], function () {
    Route::get('/providerservice/categories', [ProviderHomeController::class, 'categories']);
    Route::post('/providerservice/subcategories', [ProviderHomeController::class, 'subcategories']);
    Route::post('/providerservice/service', [ProviderHomeController::class, 'service']);
    Route::get('/totalservices', [ProviderHomeController::class, 'totalservices']);
    Route::get('/listtotalservices', [ProviderHomeController::class, 'listtotalservices']);
    Route::get('/baseservices', [ProviderHomeController::class, 'fareTypeServiceList']);

    Route::get('/check/serve/request', [ProviderServiceController::class, 'index']);
    Route::post('/update/serve/request', [ProviderServiceController::class, 'updateServe']);
    Route::patch('/update/serve/request', [ProviderServiceController::class, 'updateServe']);
    Route::post('/cancel/serve/request', [ProviderServiceController::class, 'cancelServe']);
    Route::post('/rate/serve', [ProviderServiceController::class, 'rate']);
    Route::get('/history/{type}/service', [ProviderServiceController::class, 'historyList']);
    Route::get('/history/service/{id}', [ProviderServiceController::class, 'getServiceHistorydetails']);
    Route::get('/service/disputestatus/{id}', [ProviderServiceController::class, 'getServiceRequestDispute']);
    Route::post('/history-dispute/service', [ProviderServiceController::class, 'saveServiceRequestDispute']);
    Route::get('/services/dispute', [ProviderServiceController::class, 'getdisputedetails']);
    Route::get('/dispute/service', [ProviderServiceController::class, 'getdisputedetails']);
    //PROJECT CATEGORIES
    Route::get('/service/fare_type-list', [ProviderServiceController::class, 'fareTypeList']);
    Route::get('/service/categories-list', [ProviderServiceController::class, 'categoriesList']);
    Route::get('/service/subcategories-list/{id}', [ProviderServiceController::class, 'subCategoriesList']);
    Route::get('/service/servicelist/{id}/{subcategoryid}', [ProviderServiceController::class, 'servicesList']);

    Route::post('/add/servicearea', [UserServiceController::class, 'servicearea']);
    Route::get('/servicearea/list', [UserServiceController::class, 'servicearea_list']);
});
