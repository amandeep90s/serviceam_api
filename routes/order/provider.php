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
    Route::get('/shoptype', 'V1\Order\Provider\OrderController@shoptype');
    Route::get('/check/order/request', 'V1\Order\Provider\OrderController@index');
    Route::post('/update/order/request', 'V1\Order\Provider\OrderController@updateOrderStaus');
    Route::patch('/update/order/request', 'V1\Order\Provider\OrderController@updateOrderStaus');
    Route::post('/cancel/order/request', 'V1\Order\Provider\OrderController@createDispute');
    Route::post('/rate/order', 'V1\Order\Provider\OrderController@rate');
    Route::get('/history/order', 'V1\Order\Provider\OrderController@historyList');
    Route::get('/history/order/{id}', 'V1\Order\Provider\OrderController@getOrderHistorydetails');
    Route::get('/order/disputestatus/{id}', 'V1\Order\Provider\OrderController@getOrderRequestDispute');
    Route::post('/history-dispute/order', 'V1\Order\Provider\OrderController@saveOrderRequestDispute');
    Route::get('/getdisputedetails', 'V1\Order\Provider\OrderController@getdisputedetails');
    Route::get('/order/dispute', 'V1\Order\Provider\OrderController@getdisputedetails');
});
