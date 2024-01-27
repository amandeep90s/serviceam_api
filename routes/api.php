<?php

use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\Provider\HomeController;
use App\Http\Controllers\Common\SocialLoginController;
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

Route::prefix('/admin')->group(function () {
    require_once __DIR__ . '/common/admin.php';
    require_once __DIR__ . '/service/admin.php';
});

Route::prefix('/provider')->group(function () {
    require_once __DIR__ . '/common/provider.php';
    require_once __DIR__ . '/service/provider.php';
});

Route::prefix('/user')->group(function () {
    require_once __DIR__ . '/common/user.php';
    require_once __DIR__ . '/service/user.php';
});

Route::group(['prefix' => '/v1'], function () {
    Route::post('user/appsettings', [CommonController::class, 'base']);
    Route::post('provider/appsettings', [CommonController::class, 'base']);
    Route::get('countries', [CommonController::class, 'countries_list']);
    Route::get('states/{id}', [CommonController::class, 'states_list']);
    Route::get('cities/{id}', [CommonController::class, 'cities_list']);
    Route::post('/chat', [CommonController::class, 'chat']);
    Route::post('/{provider}/social/login', [SocialLoginController::class, 'handleSocialLogin']);
    Route::post('/provider/update/location', [HomeController::class, 'update_location']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
