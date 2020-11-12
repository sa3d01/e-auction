<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\CheckApiToken;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1','namespace'=>'Api'], function () {
    Route::get('/setting', 'SettingController@index');

    Route::group(['prefix' => '/user'], function () {
        //register
        Route::post('/', 'UserController@register');
        // Verification
        Route::post('/verification-resend', 'UserController@resendCode');
        Route::post('/verify', 'UserController@verifyUser');
        Route::post('/forget-password', 'UserController@forgetPassword')->middleware(CheckApiToken::class);
        Route::post('/login', 'UserController@login');
        Route::post('/update-password', 'UserController@updatePassword')->middleware(CheckApiToken::class);
        Route::post('/update', 'UserController@update')->middleware(CheckApiToken::class);
        Route::get('/logout', 'UserController@logout')->middleware(CheckApiToken::class);

        Route::get('/profile', 'UserController@profile')->middleware(CheckApiToken::class);
        Route::get('/{id}', 'UserController@show');
    });
    Route::group(['prefix' => '/contact'], function () {
        Route::get('/types', 'ContactController@types')->middleware(CheckApiToken::class);
        Route::post('/', 'ContactController@store')->middleware(CheckApiToken::class);
    });
    Route::group(['prefix' => '/notification'], function () {
        Route::get('/', 'NotificationController@index')->middleware(CheckApiToken::class);
        Route::get('/{notification}', 'NotificationController@show')->middleware(CheckApiToken::class);
    });

});
