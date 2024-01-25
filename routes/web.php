<?php

use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LicenseController;
use App\Http\Controllers\Common\SocialLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');
Route::post('/verify', [LicenseController::class, 'verify'])->name('verify');
Route::post('/base', [CommonController::class, 'base']);
Route::post('/search', [CommonController::class, 'search']);
Route::get('/cmspage/{type}', [CommonController::class, 'cmspagetype']);
Route::get('/send/{type}/push', [SocialLoginController::class, 'push']);

// Route::get('v1/docs', ['as' => 'swagger-v1-lume.docs', 'middleware' => config('swagger-lume.routes.middleware.docs', []), 'uses' => 'V1\Common\SwaggerController@docs']);
// Route::get('/api/v1/documentation', ['as' => 'swagger-v1-lume.api', 'middleware' => config('swagger-lume.routes.middleware.api', []), 'uses' => 'V1\Common\SwaggerController@api']);
