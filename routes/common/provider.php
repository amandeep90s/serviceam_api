<?php

use App\Http\Controllers\Common\Admin\Resource\CommonController;
use App\Http\Controllers\Common\PaymentController;
use App\Http\Controllers\Common\Provider\HomeController;
use App\Http\Controllers\Common\Provider\PaymentController as ProviderPaymentController;
use App\Http\Controllers\Common\Provider\ProviderAuthController;
use App\Http\Controllers\Service\ServiceController;
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

Route::post('/login', [ProviderAuthController::class, 'login']);
Route::post('/verify', [ProviderAuthController::class, 'verify']);
Route::post('/signup', [ProviderAuthController::class, 'signup']);
Route::post('/sms/check', [ProviderAuthController::class, 'provider_sms_check']);
Route::post('/refresh', [ProviderAuthController::class, 'refresh']);
Route::post('/forgot/otp', [ProviderAuthController::class, 'forgotPasswordOTP']);
Route::post('/reset/otp', [ProviderAuthController::class, 'resetPasswordOTP']);
Route::post('countries', [HomeController::class, 'countries']);
Route::post('/send-otp', [CommonController::class, 'sendOtp']);
Route::post('/verify-otp', [CommonController::class, 'verifyOtp']);
Route::post('cities/{id}', [HomeController::class, 'cities']);
Route::post('/clear', [HomeController::class, 'clear']);

Route::group(['middleware' => 'auth:provider'], function () {
    // Authentication routes
    Route::post('/logout', [ProviderAuthController::class, 'logout']);
    Route::post('/listdocuments', [ProviderAuthController::class, 'listdocuments']);
    Route::post('/documents', [ProviderAuthController::class, 'document_store']);
    // Payment routes
    Route::post('/add/money', [PaymentController::class, 'add_money']);
    Route::get('/payment/response', [ProviderPaymentController::class, 'response']);
    Route::get('/payment/failure', [ProviderPaymentController::class, 'failure']);

    Route::get('/chat', [HomeController::class, 'get_chat']);
    Route::get('/check/request', [HomeController::class, 'index']);
    Route::post('/accept/request', [HomeController::class, 'accept_request']);
    Route::post('/cancel/request', [HomeController::class, 'cancel_request']);
    Route::get('/profile', [HomeController::class, 'show_profile']);
    Route::post('/profile', [HomeController::class, 'update_profile']);
    Route::post('/password', [HomeController::class, 'password_update']);
    Route::post('/card', [HomeController::class, 'addcard']);
    Route::get('card', [HomeController::class, 'carddetail']);
    Route::get('list', [HomeController::class, 'providerlist']);
    Route::delete('card/{id}', [HomeController::class, 'deleteCard']);
    Route::get('/wallet', [HomeController::class, 'walletlist']);
    Route::get('services/list', [HomeController::class, 'provider_services']);
    Route::post('/vehicle', [HomeController::class, 'add_vehicle']);
    Route::delete('providerdocument/{id}', [HomeController::class, 'deleteproviderdocument']);
    Route::post('/service', [HomeController::class, 'add_service']);
    Route::get('/vehicle', [HomeController::class, 'vehicle_list']);
    Route::get('/orderstatus', [HomeController::class, 'order_status']);
    Route::post('/vechile/add', [HomeController::class, 'addvechile']);
    Route::post('/vechile/addservice', [HomeController::class, 'addproviderservice']);
    Route::post('/vechile/editservice', [HomeController::class, 'editproviderservice']);
    Route::delete('/delete/service/{id}', [HomeController::class, 'deleteProviderService']);
    Route::get('/vechicle/selected-service/{id}', [HomeController::class, 'listProviderServices']);
    Route::post('/vehicle/edit', [HomeController::class, 'editvechile']);
    Route::get('/reasons', [HomeController::class, 'reasons']);
    Route::post('/updatelanguage', [HomeController::class, 'updatelanguage']);
    Route::get('/adminservices', [HomeController::class, 'adminservices']);
    Route::get('/notification', [HomeController::class, 'notification']);
    Route::get('/bankdetails/template', [HomeController::class, 'template']);
    Route::post('/addbankdetails', [HomeController::class, 'addbankdetails']);
    Route::post('/editbankdetails', [HomeController::class, 'editbankdetails']);
    Route::post('/referemail', [HomeController::class, 'refer_email']);
    Route::post('/defaultcard', [HomeController::class, 'defaultcard']);
    Route::get('/onlinestatus/{id}', [HomeController::class, 'onlinestatus']);
    Route::post('/updatelocation', [HomeController::class, 'updatelocation']);
    Route::get('/earnings/{id}', [HomeController::class, 'totalEarnings']);

    // Service routes
    Route::post('/add/servicearea', [ServiceController::class, 'servicearea']);

    Route::get('/providers', function () {
        return response()->json(['message' => Auth::guard('provider')->user()]);
    });

    // Token routes
    Route::post('device_token', [HomeController::class, 'updateDeviceToken']);
});
