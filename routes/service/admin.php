<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::group(['middleware' => 'auth:admin'], function ($app) {
    Route::group(['prefix' => 'service'], function ($app) {
        // SERVICE MAIN CATEGORIES
        Route::get('/categories', 'V1\Service\Admin\ServiceCategoryController@index');
        Route::post('/categories', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceCategoryController@store']);
        Route::get('/categories/{id}', 'V1\Service\Admin\ServiceCategoryController@show');
        Route::patch('/categories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceCategoryController@update']);
        Route::delete('/categories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceCategoryController@destroy']);
        Route::get('/categories/{id}/updateStatus', 'V1\Service\Admin\ServiceCategoryController@updateStatus');
        //PROJECT CATEGORIES
        Route::get('/categories-list', 'V1\Service\Admin\ProjectCategoryController@categoriesList');
        Route::get('/projectcategories', 'V1\Service\Admin\ProjectCategoryController@index');
        Route::post('/projectcategories', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ProjectCategoryController@store']);
        Route::get('/projectcategories/{id}', 'V1\Service\Admin\ProjectCategoryController@show');
        Route::patch('/projectcategories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ProjectCategoryController@update']);
        Route::delete('/projectcategories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ProjectCategoryController@destroy']);
        Route::get('/projectcategories/{id}/updateStatus', 'V1\Service\Admin\ProjectCategoryController@updateStatus');
        // SERVICE SUB CATEGORIES
        Route::get('/categories-list', 'V1\Service\Admin\ServiceSubCategoryController@categoriesList');
        Route::get('/subcategories', 'V1\Service\Admin\ServiceSubCategoryController@index');
        Route::post('/subcategories', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceSubCategoryController@store']);
        Route::get('/subcategories/{id}', 'V1\Service\Admin\ServiceSubCategoryController@show');
        Route::patch('/subcategories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceSubCategoryController@update']);
        Route::delete('/subcategories/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServiceSubCategoryController@destroy']);
        Route::get('/subcategories/{id}/updateStatus', 'V1\Service\Admin\ServiceSubCategoryController@updateStatus');
        // SERVICES
        Route::get('/subcategories-list/{categoryId}', 'V1\Service\Admin\ServicesController@subcategoriesList');
        Route::get('/listing', 'V1\Service\Admin\ServicesController@index');
        Route::post('/listing', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServicesController@store']);
        Route::get('/listing/{id}', 'V1\Service\Admin\ServicesController@show');
        Route::patch('/listing/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServicesController@update']);
        Route::delete('/listing/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServicesController@destroy']);
        Route::get('/listing/{id}/updateStatus', 'V1\Service\Admin\ServicesController@updateStatus');
        Route::get('/listing/{id}/provider/service/updateStatus', 'V1\Service\Admin\ServicesController@providerServiceUpdateStatus');
        Route::get('/get-service-price/{id}', 'V1\Service\Admin\ServicesController@getServicePriceCities');
        Route::post('/pricings', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\ServicesController@servicePricePost']);
        Route::get('/pricing/{service_id}/{city_id}', 'V1\Service\Admin\ServicesController@getServicePrice');
        // Dispute
        Route::post('dispute-service-search', 'V1\Service\User\ServiceController@searchServiceDispute');
        Route::get('/requestdispute', 'V1\Service\Admin\RequestDisputeController@index');
        Route::post('/requestdispute', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\RequestDisputeController@store']);
        Route::get('/requestdispute/{id}', 'V1\Service\Admin\RequestDisputeController@show');
        Route::patch('/requestdispute/{id}', ['middleware' => 'demo', 'uses' => 'V1\Service\Admin\RequestDisputeController@update']);
        Route::get('disputelist', 'V1\Service\Admin\RequestDisputeController@dispute_list');
        //request history
        Route::get('/requesthistory', 'V1\Service\User\ServiceController@requestHistory');
        Route::get('/requestschedulehistory', 'V1\Service\User\ServiceController@requestScheduleHistory');
        Route::get('/requesthistory/{id}', 'V1\Service\User\ServiceController@requestHistoryDetails');
        Route::get('/servicedocuments/{id}', 'V1\Service\User\ServiceController@webproviderservice');
        Route::get('/Servicedashboard/{id}', 'V1\Service\Admin\ServicesController@dashboarddata');
        Route::get('/requestStatementhistory', 'V1\Service\User\ServiceController@requestStatementHistory');
    });
    Route::get('user-search', 'V1\Common\User\HomeController@search_user');
    Route::get('provider-search', 'V1\Common\Provider\HomeController@search_provider');
    Route::get('getservicecity', 'V1\Service\User\ServiceController@getcity');
});
