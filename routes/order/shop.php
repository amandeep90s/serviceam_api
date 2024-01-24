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

Route::post('/login', 'V1\Order\Shop\Auth\AuthController@login');
Route::post('/refresh', 'V1\Order\Shop\Auth\AuthController@refresh');
Route::post('/forgotOtp', 'V1\Order\Shop\Auth\AuthController@forgotPasswordOTP');
Route::post('/resetOtp', 'V1\Order\Shop\Auth\AuthController@resetPasswordOTP');
Route::get('/dispatcher/autosign', 'V1\Order\Shop\Auth\AdminController@StoreAutoAssign');
Route::group(['middleware' => 'auth:shop'], function () {
    //Shops Add on
    Route::get('/addon/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@index');
    Route::post('/addons', 'V1\Order\Admin\Resource\ShopsaddonController@store');
    Route::get('/addons/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@show');
    Route::patch('/addons/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@update');
    Route::delete('/addons/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@destroy');
    Route::get('/addonslist/{id}', 'V1\Order\Admin\Resource\ShopsaddonController@addonlist');
    Route::get('/addon/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsaddonController@updateStatus');
    //Shops Category
    Route::get('/categoryindex/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@index');
    Route::post('/category', 'V1\Order\Admin\Resource\ShopscategoryController@store');
    Route::get('/category/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@show');
    Route::patch('/category/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@update');
    Route::delete('/category/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@destroy');
    Route::get('/categorylist/{id}', 'V1\Order\Admin\Resource\ShopscategoryController@categorylist');
    Route::get('/category/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopscategoryController@updateStatus');
    //Shpos Items
    Route::get('/itemsindex/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@index');
    Route::post('/items', 'V1\Order\Admin\Resource\ShopsitemsController@store');
    Route::get('/items/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@show');
    Route::patch('/items/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@update');
    Route::delete('/items/{id}', 'V1\Order\Admin\Resource\ShopsitemsController@destroy');
    Route::get('/items/{id}/updateStatus', 'V1\Order\Admin\Resource\ShopsitemsController@updateStatus');
    // Store Types
    Route::get('/storetypelist', 'V1\Order\Admin\Resource\StoretypeController@storetypelist');
    //zone
    Route::get('/zonetype/{id}', 'V1\Common\Admin\Resource\ZoneController@cityzonestype');
    //cuisine
    Route::get('/cuisinelist/{id}', 'V1\Order\Admin\Resource\CuisinesController@cuisinelist');
    //shop
    Route::get('/shops/{id}', 'V1\Order\Admin\Resource\ShopsController@show');
    Route::patch('/shops/{id}', 'V1\Order\Admin\Resource\ShopsController@update');
    //Account setting details
    Route::get('password', 'V1\Order\Shop\Auth\AdminController@password');
    Route::post('password', 'V1\Order\Shop\Auth\AdminController@password_update');
    Route::get('bankdetails/template', 'V1\Common\Provider\HomeController@template');
    Route::post('/addbankdetails', 'V1\Common\Provider\HomeController@addbankdetails');
    Route::post('/editbankdetails', 'V1\Common\Provider\HomeController@editbankdetails');
    //Dispatcher Panel
    Route::get('/dispatcher/orders', 'V1\Order\Shop\Auth\AdminController@orders');
    Route::post('/dispatcher/cancel', 'V1\Order\Shop\Auth\AdminController@cancel_orders');
    Route::post('/dispatcher/accept', 'V1\Order\Shop\Auth\AdminController@accept_orders');
    Route::post('/dispatcher/pickedup', 'V1\Order\Shop\Auth\AdminController@picked_up');
    //Wallet
    Route::get('/wallet', 'V1\Order\Shop\Auth\AdminController@wallet');
    //logout
    Route::post('/logout', 'V1\Order\Shop\Auth\AuthController@logout');
    //Dashboard
    Route::get('total/storeorder', 'V1\Order\Shop\Auth\AdminController@total_orders');
    Route::get('/transactions', 'V1\Order\Shop\ShopStatementController@statement_shop');
});
