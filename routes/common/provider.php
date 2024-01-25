<?php

use App\Http\Controllers\Common\Admin\Resource\CommonController;
use App\Http\Controllers\Common\PaymentController;
use App\Http\Controllers\Common\Provider\HomeController as ProviderHomeController;
use App\Http\Controllers\Common\Provider\PaymentController as ProviderPaymentController;
use App\Http\Controllers\Common\Provider\ProviderAuthController;
use App\Http\Controllers\Service\Admin\ServiceController;
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
Route::post('/send-otp', [CommonController::class, 'sendOtp']);
Route::post('/verify-otp', [CommonController::class, 'verifyOtp']);
Route::post('countries', [ProviderHomeController::class, 'countries']);
Route::post('cities/{id}', [ProviderHomeController::class, 'cities']);
Route::post('/clear', [ProviderHomeController::class, 'clear']);

Route::group(['middleware' => 'auth:provider'], function () {
    // Authentication routes
    Route::post('/logout', [ProviderAuthController::class, 'logout']);
    Route::post('/listdocuments', [ProviderAuthController::class, 'listdocuments']);
    Route::post('/documents', [ProviderAuthController::class, 'document_store']);
    // Payment routes
    Route::post('/add/money', [PaymentController::class, 'add_money']);
    Route::get('/payment/response', [ProviderPaymentController::class, 'response']);
    Route::get('/payment/failure', [ProviderPaymentController::class, 'failure']);

    Route::get('/check/request', [ProviderHomeController::class, 'index']);
    Route::get('/chat', [ProviderHomeController::class, 'get_chat']);
    Route::post('/accept/request', [ProviderHomeController::class, 'accept_request']);
    Route::post('/cancel/request', [ProviderHomeController::class, 'cancel_request']);
    Route::get('/profile', [ProviderHomeController::class, 'show_profile']);
    Route::post('/profile', [ProviderHomeController::class, 'update_profile']);
    Route::post('/password', [ProviderHomeController::class, 'password_update']);
    Route::post('/card', [ProviderHomeController::class, 'addcard']);
    Route::get('card', [ProviderHomeController::class, 'carddetail']);
    Route::get('list', [ProviderHomeController::class, 'providerlist']);
    Route::delete('card/{id}', [ProviderHomeController::class, 'deleteCard']);
    Route::get('/wallet', [ProviderHomeController::class, 'walletlist']);
    Route::get('services/list', [ProviderHomeController::class, 'provider_services']);
    Route::post('/vehicle', [ProviderHomeController::class, 'add_vehicle']);
    Route::delete('providerdocument/{id}', [ProviderHomeController::class, 'deleteproviderdocument']);
    Route::post('/service', [ProviderHomeController::class, 'add_service']);
    Route::get('/vehicle', [ProviderHomeController::class, 'vehicle_list']);
    Route::get('/orderstatus', [ProviderHomeController::class, 'order_status']);
    Route::post('/vechile/add', [ProviderHomeController::class, 'addvechile']);
    Route::post('/vechile/addservice', [ProviderHomeController::class, 'addproviderservice']);
    Route::post('/vechile/editservice', [ProviderHomeController::class, 'editproviderservice']);
    Route::delete('/delete/service/{id}', [ProviderHomeController::class, 'deleteProviderService']);
    Route::get('/vechicle/selected-service/{id}', [ProviderHomeController::class, 'listProviderServices']);
    Route::post('/vehicle/edit', [ProviderHomeController::class, 'editvechile']);
    Route::get('/reasons', [ProviderHomeController::class, 'reasons']);
    Route::post('/updatelanguage', [ProviderHomeController::class, 'updatelanguage']);
    Route::get('/adminservices', [ProviderHomeController::class, 'adminservices']);
    Route::get('/notification', [ProviderHomeController::class, 'notification']);
    Route::get('/bankdetails/template', [ProviderHomeController::class, 'template']);
    Route::post('/addbankdetails', [ProviderHomeController::class, 'addbankdetails']);
    Route::post('/editbankdetails', [ProviderHomeController::class, 'editbankdetails']);
    Route::post('/referemail', [ProviderHomeController::class, 'refer_email']);
    Route::post('/defaultcard', [ProviderHomeController::class, 'defaultcard']);
    Route::get('/onlinestatus/{id}', [ProviderHomeController::class, 'onlinestatus']);
    Route::post('/updatelocation', [ProviderHomeController::class, 'updatelocation']);
    Route::get('/earnings/{id}', [ProviderHomeController::class, 'totalEarnings']);

    // Service routes
    Route::post('/add/servicearea', [ServiceController::class, 'servicearea']);

    Route::get('/providers', function () {
        return response()->json(['message' => Auth::guard('provider')->user()]);
    });

    // Token routes
    Route::post('device_token', [ProviderHomeController::class, 'updateDeviceToken']);
});
