<?php

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

// Route::get('v1/docs', ['as' => 'swagger-v1-lume.docs', 'middleware' => config('swagger-lume.routes.middleware.docs', []), 'uses' => 'V1\Common\SwaggerController@docs']);
// Route::get('/api/v1/documentation', ['as' => 'swagger-v1-lume.api', 'middleware' => config('swagger-lume.routes.middleware.api', []), 'uses' => 'V1\Common\SwaggerController@api']);
