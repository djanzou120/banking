<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/login', 'App\Http\Controllers\Auth\LoginController@index');

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/account', 'App\Http\Controllers\AccountController@index');
    Route::get('/account/{accountId}', 'App\Http\Controllers\AccountController@show');
    Route::post('/account', 'App\Http\Controllers\AccountController@create');
    Route::put('/account/{accountId}', 'App\Http\Controllers\AccountController@update');
    Route::delete('/account/{accountId}', 'App\Http\Controllers\AccountController@delete');
});
