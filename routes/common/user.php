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

Route::post('/login', 'V1\Common\User\UserAuthController@login');
Route::post('/verify', 'V1\Common\User\UserAuthController@verify');
Route::post('/signup', 'V1\Common\User\UserAuthController@signup');
Route::post('/sms/check', 'V1\Common\User\UserAuthController@user_sms_check');
Route::post('/send-otp', 'V1\Common\CommonController@sendOtp');
Route::post('/verify-otp', 'V1\Common\CommonController@verifyOtp');
Route::post('/refresh', 'V1\Common\User\UserAuthController@refresh');
Route::post('/forgot/otp', 'V1\Common\User\UserAuthController@forgotPasswordOTP');
Route::post('/reset/otp', 'V1\Common\User\UserAuthController@resetPasswordOTP');
Route::get('/logout', 'V1\Common\User\UserAuthController@logout');
Route::post('countries', 'V1\Common\User\HomeController@countries');
Route::post('/socket', 'V1\Common\User\SocketController@checkDomain');
Route::group(['middleware' => 'auth:user'], function () {
    Route::get('cities', 'V1\Common\User\HomeController@cities');
    Route::get('promocodes', 'V1\Common\User\HomeController@promocode');
    Route::get('/reasons', 'V1\Common\User\HomeController@reasons');
    Route::get('/ongoing', 'V1\Common\User\HomeController@ongoing_services');
    Route::get('/users', function () {
        return response()->json(['message' => Auth::guard('user')->user(),]);
    });
    Route::post('/logout', 'V1\Common\User\UserAuthController@logout');
    Route::get('/chat', 'V1\Common\User\HomeController@get_chat');
    Route::get('/menus', 'V1\Common\User\HomeController@index');
    Route::post('/address/add', 'V1\Common\User\HomeController@addmanageaddress');
    Route::patch('/address/update', 'V1\Common\User\HomeController@updatemanageaddress');
    Route::get('/address', 'V1\Common\User\HomeController@listmanageaddress');
    Route::delete('/address/{id}', 'V1\Common\User\HomeController@deletemanageaddress');
    Route::get('/profile', 'V1\Common\User\HomeController@show_profile');
    Route::post('/profile', 'V1\Common\User\HomeController@update_profile');
    Route::post('password', 'V1\Common\User\HomeController@password_update');
    Route::post('card', 'V1\Common\User\HomeController@addcard');
    Route::get('card', 'V1\Common\User\HomeController@carddetail');
    Route::get('walletlist', 'V1\Common\User\HomeController@userlist');
    Route::delete('card/{id}', 'V1\Common\User\HomeController@deleteCard');
    Route::post('/add/money', 'V1\Common\PaymentController@add_money');
    Route::get('/payment/response', 'V1\Common\User\PaymentController@response');
    Route::get('/payment/failure', 'V1\Common\User\PaymentController@failure');
    Route::get('/wallet', 'V1\Common\User\HomeController@walletlist');
    Route::get('/orderstatus', 'V1\Common\User\HomeController@order_status');
    Route::post('/updatelanguage', 'V1\Common\User\HomeController@updatelanguage');
    Route::get('/service/{id}', 'V1\Common\User\HomeController@service');
    Route::get('/service_city_price/{id}', 'V1\Common\User\HomeController@service_city_price');
    Route::get('/notification', 'V1\Common\User\HomeController@notification');
    Route::get('/promocode/{service}', 'V1\Common\User\HomeController@listpromocode');
    Route::post('/city', 'V1\Common\User\HomeController@city');
    Route::post('/defaultcard', 'V1\Common\User\HomeController@defaultcard');
    Route::post('device_token', 'V1\Common\User\HomeController@updateDeviceToken');
});
Route::post('/account/kit', 'V1\Common\User\SocialLoginController@account_kit');
