<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function(){
	Route::group(['prefix' => 'user'], function () {
		Route::get('data', [UserController::class, 'userData']);
		Route::get('logout', [UserController::class, 'logout']);
	});
});