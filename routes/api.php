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
    Route::group(['prefix' => '/general'], function () {
        Route::get('/setting', 'SettingController@index');
        Route::get('/partners', 'SettingController@partners');
    });
    Route::group(['prefix' => '/package'], function () {
        Route::get('/', 'PackageController@index');
        Route::get('/{package}', 'PackageController@show');
    });
    Route::group(['prefix' => '/transfer'], function () {
        Route::post('/', 'TransferController@transfer')->middleware(CheckApiToken::class);
    });
    Route::group(['prefix' => '/user'], function () {
        // Verification
        Route::post('/validate-email', 'UserController@authMail');
        Route::post('/verify', 'UserController@verifyUser');
        //register
        Route::post('/update', 'UserController@update')->middleware(CheckApiToken::class);
        Route::get('/logout', 'UserController@logout')->middleware(CheckApiToken::class);
        Route::get('/profile', 'UserController@profile')->middleware(CheckApiToken::class);
        Route::get('/{id}', 'UserController@show');
    });
    Route::group(['prefix' => '/contact'], function () {
        Route::post('/', 'ContactController@store')->middleware(CheckApiToken::class);
    });
});
