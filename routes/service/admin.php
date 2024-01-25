<?php

use App\Http\Controllers\Common\Provider\HomeController as ProviderHomeController;
use App\Http\Controllers\Common\User\HomeController as UserHomeController;
use App\Http\Controllers\Service\Admin\ProjectCategoryController;
use App\Http\Controllers\Service\Admin\ServiceCategoryController;
use App\Http\Controllers\Service\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Service\Admin\ServiceRequestDisputeController;
use App\Http\Controllers\Service\Admin\ServiceSubCategoryController;
use App\Http\Controllers\Service\User\ServiceController as UserServiceController;
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
    Route::group(['prefix' => 'service'], function () {
        // SERVICE MAIN CATEGORIES
        Route::apiResource('/categories', ServiceCategoryController::class)->middleware([
            'store' => 'demo',
            'update' => 'demo',
            'destroy' => 'demo'
        ]);
        Route::get('/categories/{id}/updateStatus', [ServiceCategoryController::class, 'updateStatus']);

        //PROJECT CATEGORIES
        Route::apiResource('/projectcategories', ProjectCategoryController::class)->middleware([
            'store' => 'demo',
            'update' => 'demo',
            'destroy' => 'demo'
        ]);
        Route::get('/categories-list', [ProjectCategoryController::class, 'categoriesList']);
        Route::get('/projectcategories/{id}/updateStatus', [ProjectCategoryController::class, 'updateStatus']);

        // SERVICE SUB CATEGORIES
        Route::apiResource('/subcategories', ServiceSubCategoryController::class)->middleware([
            'store' => 'demo',
            'update' => 'demo',
            'destroy' => 'demo'
        ]);
        Route::get('/categories-list', [ServiceSubCategoryController::class, 'categoriesList']);
        Route::get('/subcategories/{id}/updateStatus', [ServiceSubCategoryController::class, 'updateStatus']);

        // SERVICES
        Route::apiResource('/listing', AdminServiceController::class)->middleware([
            'store' => 'demo',
            'update' => 'demo',
            'destroy' => 'demo'
        ]);
        Route::get('/subcategories-list/{categoryId}', [AdminServiceController::class, 'subcategoriesList']);
        Route::get('/listing/{id}/updateStatus', [AdminServiceController::class, 'updateStatus']);
        Route::get('/listing/{id}/provider/service/updateStatus', [AdminServiceController::class, 'providerServiceUpdateStatus']);
        Route::get('/get-service-price/{id}', [AdminServiceController::class, 'getServicePriceCities']);
        Route::post('/pricings', [AdminServiceController::class, 'servicePricePost'])->middleware(['demo']);
        Route::get('/pricing/{service_id}/{city_id}', [AdminServiceController::class, 'getServicePrice']);

        // Dispute
        Route::post('dispute-service-search', [UserServiceController::class, 'searchServiceDispute']);
        Route::apiResource('/requestdispute', ServiceRequestDisputeController::class)->middleware([
            'store' => 'demo',
            'update' => 'demo'
        ]);
        Route::get('disputelist', [ServiceRequestDisputeController::class, 'dispute_list']);

        // REQUEST HISTORY
        Route::get('/requesthistory', [UserServiceController::class, 'requestHistory']);
        Route::get('/requestschedulehistory', [UserServiceController::class, 'requestScheduleHistory']);
        Route::get('/requesthistory/{id}', [UserServiceController::class, 'requestHistoryDetails']);
        Route::get('/servicedocuments/{id}', [UserServiceController::class, 'webproviderservice']);
        Route::get('/Servicedashboard/{id}', [AdminServiceController::class, 'dashboarddata']);
        Route::get('/requestStatementhistory', [UserServiceController::class, 'requestStatementHistory']);
    });
    // User Search
    Route::get('user-search', [UserHomeController::class, 'search_user']);
    // Provider Search
    Route::get('provider-search', [ProviderHomeController::class, 'search_provider']);
    // Get User Service City
    Route::get('getservicecity', [UserServiceController::class, 'getcity']);
});
