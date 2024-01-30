<?php

use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\PaymentController;
use App\Http\Controllers\Common\User\HomeController as UserHomeController;
use App\Http\Controllers\Common\User\SocialLoginController;
use App\Http\Controllers\Common\User\SocketController;
use App\Http\Controllers\Common\User\UserAuthController;
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

Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/verify', [UserAuthController::class, 'verify']);
Route::post('/signup', [UserAuthController::class, 'signup']);
Route::post('/sms/check', [UserAuthController::class, 'user_sms_check']);
Route::post('/refresh', [UserAuthController::class, 'refresh']);
Route::post('/forgot/otp', [UserAuthController::class, 'forgotPasswordOTP']);
Route::post('/reset/otp', [UserAuthController::class, 'resetPasswordOTP']);
Route::post('/send-otp', [CommonController::class, 'sendOtp']);
Route::post('/verify-otp', [CommonController::class, 'verifyOtp']);
Route::post('countries', [UserHomeController::class, 'countries']);
Route::post('/socket', [SocketController::class, 'checkDomain']);
Route::post('/account/kit', [SocialLoginController::class, 'account_kit']);

Route::middleware(['auth:user'])->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout']);
    Route::get('/cities', [UserHomeController::class, 'cities']);
    Route::get('/promocodes', [UserHomeController::class, 'promocode']);
    Route::get('/reasons', [UserHomeController::class, 'reasons']);
    Route::get('/ongoing', [UserHomeController::class, 'ongoing_services']);
    Route::get('/chat', [UserHomeController::class, 'get_chat']);
    Route::get('/menus', [UserHomeController::class, 'index']);
    Route::post('/address/add', [UserHomeController::class, 'addmanageaddress']);
    Route::patch('/address/update', [UserHomeController::class, 'updatemanageaddress']);
    Route::get('/address', [UserHomeController::class, 'listmanageaddress']);
    Route::delete('/address/{id}', [UserHomeController::class, 'deletemanageaddress']);
    Route::get('/profile', [UserHomeController::class, 'show_profile']);
    Route::post('/profile', [UserHomeController::class, 'update_profile']);
    Route::post('/password', [UserHomeController::class, 'password_update']);
    Route::post('/card', [UserHomeController::class, 'addcard']);
    Route::get('/card', [UserHomeController::class, 'carddetail']);
    Route::get('/walletlist', [UserHomeController::class, 'userlist']);
    Route::delete('/card/{id}', [UserHomeController::class, 'deleteCard']);
    Route::post('/add/money', [PaymentController::class, 'add_money']);
    Route::get('/payment/response', [PaymentController::class, 'response']);
    Route::get('/payment/failure', [PaymentController::class, 'failure']);
    Route::get('/wallet', [UserHomeController::class, 'walletlist']);
    Route::get('/orderstatus', [UserHomeController::class, 'order_status']);
    Route::post('/updatelanguage', [UserHomeController::class, 'updatelanguage']);
    Route::get('/service/{id}', [UserHomeController::class, 'service']);
    Route::get('/service_city_price/{id}', [UserHomeController::class, 'service_city_price']);
    Route::get('/notification', [UserHomeController::class, 'notification']);
    Route::get('/promocode/{service}', [UserHomeController::class, 'listpromocode']);
    Route::post('/city', [UserHomeController::class, 'city']);
    Route::post('/defaultcard', [UserHomeController::class, 'defaultcard']);
    Route::post('/device_token', [UserHomeController::class, 'updateDeviceToken']);
    Route::get('/users', function () {
        return response()->json(['message' => Auth::guard('user')->user(),]);
    });
});
