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
        Route::get('/asks', 'SettingController@asks');
    });

    Route::group(['prefix' => '/drop_down'], function () {
        Route::get('/category', 'DropDownController@categories');
        Route::get('/partners', 'DropDownController@partners');
        Route::get('/mark', 'DropDownController@marks');
        Route::get('/mark/{id}/model', 'DropDownController@models');
        Route::get('/item_status_list', 'DropDownController@itemStatus');
        Route::get('/city', 'DropDownController@cities');
        Route::get('/fetes', 'DropDownController@fetes');
        Route::get('/color', 'DropDownController@colors');
        Route::get('/scan_status', 'DropDownController@scanStatus');
        Route::get('/paper_status', 'DropDownController@paperStatus');
    });

    Route::group(['prefix' => '/auction_types'], function () {
        Route::get('/', 'AuctionTypeController@auctionTypes');
    });

    Route::group(['prefix' => '/feed_back'], function () {
        Route::get('/', 'FeedBackController@index');
        Route::post('/', 'FeedBackController@store')->middleware(CheckApiToken::class);
    });
    Route::group(['prefix' => '/contact'], function () {
        Route::post('/', 'ContactController@store')->middleware(CheckApiToken::class);
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
        Route::post('/validate-auth', 'UserController@authPhoneAndMail');
        Route::post('/verify', 'UserController@verifyUser');
        //register
        Route::post('/update', 'UserController@update')->middleware(CheckApiToken::class);
        Route::get('/logout', 'UserController@logout')->middleware(CheckApiToken::class);
        Route::get('/profile', 'UserController@profile')->middleware(CheckApiToken::class);
        Route::get('/{id}', 'UserController@show');
    });


    Route::group(['prefix' => '/item'], function () {
        Route::post('/upload_images', 'ItemController@uploadImages')->middleware(CheckApiToken::class);
        Route::post('/', 'ItemController@store')->middleware(CheckApiToken::class);
    });

    Route::group(['prefix' => '/notification'], function () {
        Route::get('/', 'NotificationController@index')->middleware(CheckApiToken::class);
        Route::get('/{notification}', 'NotificationController@show')->middleware(CheckApiToken::class);
    });

});
