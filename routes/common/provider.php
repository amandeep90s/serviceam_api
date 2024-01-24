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

Route::post('/login', 'V1\Common\Provider\ProviderAuthController@login');
Route::post('/verify', 'V1\Common\Provider\ProviderAuthController@verify');
Route::post('/send-otp', 'V1\Common\CommonController@sendOtp');
Route::post('/verify-otp', 'V1\Common\CommonController@verifyOtp');
Route::post('/signup', 'V1\Common\Provider\ProviderAuthController@signup');
Route::post('/sms/check', 'V1\Common\Provider\ProviderAuthController@provider_sms_check');
Route::post('/refresh', 'V1\Common\Provider\ProviderAuthController@refresh');
Route::post('/forgot/otp', 'V1\Common\Provider\ProviderAuthController@forgotPasswordOTP');
Route::post('/reset/otp', 'V1\Common\Provider\ProviderAuthController@resetPasswordOTP');
Route::post('countries', 'V1\Common\Provider\HomeController@countries');
Route::post('cities/{id}', 'V1\Common\Provider\HomeController@cities');
Route::group(['middleware' => 'auth:provider'], function ($app) {
    Route::post('/logout', 'V1\Common\Provider\ProviderAuthController@logout');
    Route::get('/chat', 'V1\Common\Provider\HomeController@get_chat');
    Route::get('/check/request', 'V1\Common\Provider\HomeController@index');
    Route::post('/accept/request', 'V1\Common\Provider\HomeController@accept_request');
    Route::post('/cancel/request', 'V1\Common\Provider\HomeController@cancel_request');
    Route::post('/listdocuments', 'V1\Common\Provider\ProviderAuthController@listdocuments');
    Route::post('/documents', 'V1\Common\Provider\ProviderAuthController@document_store');
    Route::get('/profile', 'V1\Common\Provider\HomeController@show_profile');
    Route::post('/profile', 'V1\Common\Provider\HomeController@update_profile');
    Route::post('/password', 'V1\Common\Provider\HomeController@password_update');
    Route::post('/card', 'V1\Common\Provider\HomeController@addcard');
    Route::get('card', 'V1\Common\Provider\HomeController@carddetail');
    Route::get('list', 'V1\Common\Provider\HomeController@providerlist');
    Route::delete('card/{id}', 'V1\Common\Provider\HomeController@deleteCard');
    Route::post('/add/money', 'V1\Common\PaymentController@add_money');
    Route::get('/payment/response', 'V1\Common\Provider\PaymentController@response');
    Route::get('/payment/failure', 'V1\Common\Provider\PaymentController@failure');
    Route::get('/wallet', 'V1\Common\Provider\HomeController@walletlist');
    Route::get('services/list', 'V1\Common\Provider\HomeController@provider_services');
    Route::post('/vehicle', 'V1\Common\Provider\HomeController@add_vehicle');
    Route::delete('providerdocument/{id}', 'V1\Common\Provider\HomeController@deleteproviderdocument');
    Route::post('/service', 'V1\Common\Provider\HomeController@add_service');
    Route::get('/vehicle', 'V1\Common\Provider\HomeController@vehicle_list');
    Route::get('/orderstatus', 'V1\Common\Provider\HomeController@order_status');
    Route::post('/vechile/add', 'V1\Common\Provider\HomeController@addvechile');
    Route::post('/vechile/addservice', 'V1\Common\Provider\HomeController@addproviderservice');
    Route::post('/vechile/editservice', 'V1\Common\Provider\HomeController@editproviderservice');
    Route::delete('/delete/service/{id}', 'V1\Common\Provider\HomeController@deleteProviderService');
    Route::get('/vechicle/selected-service/{id}', 'V1\Common\Provider\HomeController@listProviderServices');
    Route::post('/vehicle/edit', 'V1\Common\Provider\HomeController@editvechile');
    Route::get('/reasons', 'V1\Common\Provider\HomeController@reasons');
    Route::post('/updatelanguage', 'V1\Common\Provider\HomeController@updatelanguage');
    Route::get('/adminservices', 'V1\Common\Provider\HomeController@adminservices');
    Route::get('/notification', 'V1\Common\Provider\HomeController@notification');
    Route::get('/bankdetails/template', 'V1\Common\Provider\HomeController@template');
    Route::post('/addbankdetails', 'V1\Common\Provider\HomeController@addbankdetails');
    Route::post('/editbankdetails', 'V1\Common\Provider\HomeController@editbankdetails');
    Route::post('/referemail', 'V1\Common\Provider\HomeController@refer_email');
    Route::post('/defaultcard', 'V1\Common\Provider\HomeController@defaultcard');
    Route::get('/onlinestatus/{id}', 'V1\Common\Provider\HomeController@onlinestatus');
    Route::post('/updatelocation', 'V1\Common\Provider\HomeController@updatelocation');
    Route::get('/earnings/{id}', 'V1\Common\Provider\HomeController@totalEarnings');
    Route::post('/add/servicearea', 'V1\Service\User\ServiceController@servicearea');
    Route::get('/providers', function () {
        return response()->json(['message' => Auth::guard('provider')->user(),]);
    });
    Route::post('device_token', 'V1\Common\Provider\HomeController@updateDeviceToken');
});
Route::post('/clear', 'V1\Common\Provider\HomeController@clear');
