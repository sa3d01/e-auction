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
        Route::get('/auctions', 'AuctionController@auctions');
        Route::get('/auctions/{id}', 'AuctionController@auctionItems');
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

    Route::post('/transfer', 'TransferController@transfer')->middleware(CheckApiToken::class);
    Route::post('/refund', 'TransferController@refund')->middleware(CheckApiToken::class);

    Route::group(['prefix' => '/user'], function () {
        // Verification
        Route::post('/validate-auth', 'UserController@authPhoneAndMail');
        Route::post('/verify', 'UserController@verifyUser');
        //register
        Route::post('/update', 'UserController@update')->middleware(CheckApiToken::class);
        Route::get('/logout', 'UserController@logout')->middleware(CheckApiToken::class);
        Route::get('/profile', 'UserController@profile')->middleware(CheckApiToken::class);
        Route::get('/favourite', 'UserController@favourite')->middleware(CheckApiToken::class);
        Route::get('/auction_reports', 'UserController@auctionReports')->middleware(CheckApiToken::class);
        Route::get('/products_reports', 'UserController@productsReports')->middleware(CheckApiToken::class);
        Route::get('/my-paid-items', 'UserController@myPaidItems')->middleware(CheckApiToken::class);
        Route::get('/{id}', 'UserController@show');
    });

    Route::get('/negotiation_items', 'NegotiationController@myNegotiationItems')->middleware(CheckApiToken::class);

    Route::group(['prefix' => '/item'], function () {
        Route::post('/upload_images', 'ItemController@uploadImages')->middleware(CheckApiToken::class);
        Route::post('/', 'ItemController@store')->middleware(CheckApiToken::class);
        Route::get('/live', 'BidController@liveItem');
        Route::post('/search', 'AuctionController@search');
        Route::post('/{item}/bid', 'BidController@bid')->middleware(CheckApiToken::class);
        Route::post('/{item}/offer', 'NegotiationController@sendOffer')->middleware(CheckApiToken::class);
        Route::post('/{item}/accept_offer/{offer}', 'NegotiationController@acceptOffer')->middleware(CheckApiToken::class);
        Route::post('/{item}/refuse_offer', 'NegotiationController@refuseOffer')->middleware(CheckApiToken::class);
        Route::get('/{item}/offer', 'NegotiationController@itemOffers')->middleware(CheckApiToken::class);
        Route::post('/{item}/direct_pay', 'NegotiationController@directPay')->middleware(CheckApiToken::class);
    });

    Route::group(['prefix' => '/home'], function () {
        Route::get('/', 'AuctionController@index');
        Route::get('item/{item_id}', 'AuctionController@show');
        Route::post('item/{item}/favourite', 'ItemController@favouriteModification')->middleware(CheckApiToken::class);
        Route::get('item/{item_id}/reports', 'AuctionController@reports');
        Route::get('my_items', 'AuctionController@my_items')->middleware(CheckApiToken::class);
        Route::get('my_auctions', 'AuctionController@my_auctions')->middleware(CheckApiToken::class);
    });

    Route::group(['prefix' => '/notification'], function () {
        Route::get('/', 'NotificationController@index')->middleware(CheckApiToken::class);
        Route::get('/{notification}', 'NotificationController@show')->middleware(CheckApiToken::class);
    });

});
