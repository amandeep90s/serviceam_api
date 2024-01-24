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

Route::group(['middleware' => 'auth:admin'], function () {
    Route::group(['prefix' => 'store'], function () {
        // Store Type
        Route::get('/storetypes', 'V1\Order\Admin\Resource\StoretypeController@index');
        Route::post('/storetypes', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\StoretypeController@store']);
        Route::get('/storetypes/{id}', 'V1\Order\Admin\Resource\StoretypeController@show');
        Route::patch('/storetypes/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\StoretypeController@update']);
        Route::delete('/storetypes/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\StoretypeController@destroy']);
        Route::get('/storetypelist', 'V1\Order\Admin\Resource\StoretypeController@storetypelist');
        Route::get('/storetypes/{id}/updateStatus', 'V1\Order\Admin\Resource\StoretypeController@updateStatus');
        Route::get('/orderdocuments/{id}', 'V1\Order\Admin\Resource\StoretypeController@webproviderservice');
        Route::get('/pricing/{store_type_id}/{city_id}', 'V1\Order\Admin\Resource\StoretypeController@getstorePrice');
        Route::post('/pricings', 'V1\Order\Admin\Resource\StoretypeController@storePricePost');
        // Cuisines
        Route::get('/cuisines', 'V1\Order\Admin\Resource\CuisinesController@index');
        Route::post('/cuisines', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\CuisinesController@store']);
        Route::get('/cuisines/{id}', 'V1\Order\Admin\Resource\CuisinesController@show');
        Route::patch('/cuisines/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\CuisinesController@update']);
        Route::delete('/cuisines/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\CuisinesController@destroy']);
        Route::get('/cuisinelist/{id}', 'V1\Order\Admin\Resource\CuisinesController@cuisinelist');
        Route::get('/cuisines/{id}/updateStatus', 'V1\Order\Admin\Resource\CuisinesController@updateStatus');
        //Shops
        Route::get('/shops', 'V1\Order\Admin\Resource\ShopsController@index');
        Route::post('/shops', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsController@store']);
        Route::get('/shops/{id}', 'V1\Order\Admin\Resource\ShopsController@show');
        Route::patch('/shops/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsController@update']);
        Route::delete('/shops/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsController@destroy']);
        Route::get('/shops/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsController@updateStatus');
        Route::get('/shops/wallet/{id}', 'V1\Order\Admin\Resource\ShopsController@walletDetails');
        Route::get('shops/storelogs/{id}', 'V1\Order\Admin\Resource\ShopsController@logDetails');
        Route::get('/get-store-price', 'V1\Order\Admin\Resource\ShopsController@getStorePriceCities');
        //Shops Add on
        Route::get('/addon/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@index');
        Route::post('/addons', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsaddonController@store']);
        Route::get('/addons/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@show');
        Route::patch('/addons/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsaddonController@update']);
        Route::delete('/addons/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsaddonController@destroy']);
        Route::get('/addonslist/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@addonlist');
        Route::get('/addon/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsaddonController@updateStatus');
        //Shops Category
        Route::get('/categoryindex/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@index');
        Route::post('/category', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopscategoryController@store']);
        Route::get('/category/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@show');
        Route::patch('/category/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopscategoryController@update']);
        Route::delete('/category/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopscategoryController@destroy']);
        Route::get('/categorylist/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@categorylist');
        Route::get('/category/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopscategoryController@updateStatus');
        //Shpos Items
        Route::get('/itemsindex/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@index');
        Route::post('/items', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsitemsController@store']);
        Route::get('/items/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@show');
        Route::patch('/items/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsitemsController@update']);
        Route::delete('/items/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\ShopsitemsController@destroy']);
        Route::get('/items/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsitemsController@updateStatus');
        //request history
        Route::get('/requesthistory', 'V1\Order\User\HomeController@requestHistory');
        Route::get('/requestschedulehistory', 'V1\Order\User\HomeController@requestScheduleHistory');
        Route::get('/requesthistory/{id}', 'V1\Order\User\HomeController@requestHistoryDetails');
        Route::get('/requestStatementhistory', 'V1\Order\User\HomeController@requestStatementHistory');
        Route::get('/storeStatementHistory', 'V1\Order\Admin\Resource\ShopsController@storeStatementHistory');
        Route::get('/items/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsitemsController@updateStatus');
        Route::get('/items/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsitemsController@updateStatus');
        //shop Dispute
        Route::post('dispute-order-search', 'V1\Order\Admin\Resource\StoreDisputeController@searchOrderDispute');
        Route::get('/requestdispute', 'V1\Order\Admin\Resource\StoreDisputeController@index');
        Route::post('/requestdispute', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\StoreDisputeController@store']);
        Route::get('/requestdispute/{id}', 'V1\Order\Admin\Resource\StoreDisputeController@show');
        Route::patch('/requestdispute/{id}', ['middleware' => 'demo', 'uses' => 'V1\Order\Admin\Resource\StoreDisputeController@update']);
        Route::get('disputelist', 'V1\Order\Admin\Resource\StoreDisputeController@dispute_list');
        Route::get('findprovider/{store_id}', 'V1\Order\Admin\Resource\StoreDisputeController@findprovider');
        //dashboard
        Route::get('/dashboards/{id}', 'V1\Order\Admin\Resource\ShopsController@dashboarddata');
        Route::get('/Storedashboard/{id}', 'V1\Order\Admin\Resource\ShopsController@storedashboard');
    });
    Route::get('getordercity', 'V1\Order\Admin\Resource\StoretypeController@getcity');
});
