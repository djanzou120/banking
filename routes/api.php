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
    Route::post('/account', 'App\Http\Controllers\AccountController@store');
    Route::put('/account/{accountId}', 'App\Http\Controllers\AccountController@update');
    Route::delete('/account/{accountId}', 'App\Http\Controllers\AccountController@delete');
    Route::get('/account/sold/{accountId}', 'App\Http\Controllers\AccountController@getSold');
    Route::get('/account/story/{accountId}', 'App\Http\Controllers\AccountController@story');

    Route::get('/customer', 'App\Http\Controllers\CustomerController@index');
    Route::get('/customer/{customerId}', 'App\Http\Controllers\CustomerController@show');
    Route::post('/customer', 'App\Http\Controllers\CustomerController@store');
    Route::put('/customer/{customerId}', 'App\Http\Controllers\CustomerController@update');
    Route::delete('/customer/{customerId}', 'App\Http\Controllers\CustomerController@delete');
    Route::get('/customer/sold/{customerId}', 'App\Http\Controllers\CustomerController@getSold');
    Route::get('/customer/subsold/{customerId}', 'App\Http\Controllers\CustomerController@getSubSold');

    Route::post('/transaction/send', 'App\Http\Controllers\TransactionController@sendToAccount');
    Route::post('/transaction/deposit', 'App\Http\Controllers\TransactionController@depositToAccount');
});
