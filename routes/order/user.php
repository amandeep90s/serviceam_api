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
    Route::get('/store/list/{id}', 'V1\Order\User\HomeController@store_list');
    Route::get('/store/cusines/{id}', 'V1\Order\User\HomeController@cusine_list');
    Route::get('/store/details/{id}', 'V1\Order\User\HomeController@store_details');
    //address
    Route::post(
        '/store/address/add',
        'V1\Common\User\HomeController@addmanageaddress'
    );
    Route::get('/store/useraddress', 'V1\Order\User\HomeController@useraddress');
    Route::delete('/store/address/{id}', 'V1\Common\User\HomeController@deletemanageaddress');
    Route::get('/store/address/{id}', 'V1\Common\User\HomeController@editmanageaddress');
    //addons
    Route::get('/store/cart-addons/{id}', 'V1\Order\User\HomeController@cart_addons');
    Route::get('/store/show-addons/{id}', 'V1\Order\User\HomeController@show_addons');
    Route::post('/store/addcart', 'V1\Order\User\HomeController@addcart');
    Route::post('/store/removecart', 'V1\Order\User\HomeController@removecart');
    Route::get('/store/cartlist', 'V1\Order\User\HomeController@viewcart');
    Route::get('/store/promocodelist', 'V1\Order\User\HomeController@promocodelist');
    Route::post('/order/cancel/request', 'V1\Order\User\HomeController@cancelOrder');
    Route::post('/store/checkout', 'V1\Order\User\HomeController@checkout');
    Route::get('/store/check/request', 'V1\Order\User\HomeController@status');
    Route::get('/store/order/{id}', 'V1\Order\User\HomeController@orderdetails');
    Route::post('/store/order/rating', 'V1\Order\User\HomeController@orderdetailsRating');
    Route::get('/trips-history/order', 'V1\Order\User\HomeController@tripsList');
    Route::get('/trips-history/order/{id}', 'V1\Order\User\HomeController@getOrderHistorydetails');
    Route::get('/upcoming/trips/order', 'V1\Order\User\HomeController@tripsUpcomingList');
    Route::get('/upcoming/trips/order/{id}', 'V1\Order\User\HomeController@orderdetails');
    Route::get('/order/dispute', 'V1\Order\Provider\OrderController@getUserdisputedetails');
    Route::get('/order/search/{id}', 'V1\Order\User\HomeController@search');
    Route::get('/getUserdisputedetails', 'V1\Order\Provider\OrderController@getdisputedetails');
    Route::post('/order/dispute', 'V1\Order\User\HomeController@order_request_dispute');
    Route::get('/order/disputestatus/{id}', 'V1\Order\User\HomeController@get_order_request_dispute');
});
