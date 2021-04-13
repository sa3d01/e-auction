<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('/admin')->name('admin.')->namespace('Admin')->group(function(){
    Route::namespace('Auth')->group(function(){
        Route::get('/login','LoginController@showLoginForm')->name('login');
        Route::post('/login','LoginController@login')->name('login.submit');
        Route::post('/logout','LoginController@logout')->name('logout');
        Route::get('/password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('/password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('/password/reset','ResetPasswordController@reset')->name('password.update');
    });

    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/setting', 'HomeController@setting')->name('setting');
    Route::post('/setting', 'HomeController@update_setting')->name('setting.update');
    Route::get('get_models/{mark_id}', 'DropDownController@get_models')->name('mark.get_models');

    Route::get('admin/profile', 'AdminController@profile')->name('profile');
    Route::post('admin/update_profile/{id}', 'AdminController@update_profile')->name('update_profile');
    Route::resource('admin', 'AdminController');
    Route::post('/admin/{id}', 'AdminController@update')->name('update');
    Route::get('admin/activate/{id}', 'AdminController@activate')->name('admin.activate');

    Route::resource('role', 'RoleController');
    Route::post('/role/{id}', 'RoleController@update')->name('update');

    Route::post('user/{id}', 'UserController@update')->name('user.update');
    Route::resource('user', 'UserController');
    Route::get('user/activate/{id}', 'UserController@activate')->name('user.activate');

    Route::post('drop_down/{id}', 'DropDownController@update')->name('drop_down.update');
    Route::get('drop_down/new/{class}', 'DropDownController@new')->name('drop_down.new');
    Route::get('drop_down/{class}/class', 'DropDownController@list')->name('drop_down.list');
    Route::resource('drop_down', 'DropDownController');
    Route::get('drop_down/activate/{id}', 'DropDownController@activate')->name('drop_down.activate');

    Route::get('pre_live_items', 'ItemController@preLiveItems')->name('pre_live_items');
    Route::get('live_items', 'ItemController@liveItems')->name('live_items');
    Route::get('expired_items', 'ItemController@expiredItems')->name('expired_items');

    Route::get('item/status/{status}', 'ItemController@items')->name('item.status');
    Route::get('item/{id}/reject', 'ItemController@reject')->name('item.reject');
    Route::get('item/{id}/accept', 'ItemController@accept')->name('item.accept');
    Route::get('item/{id}/reports', 'ItemController@reports')->name('item.reports');
    Route::post('item/{id}/images', 'ItemController@uploadImages')->name('item.upload-images');
    Route::get('item/{id}/auction_price', 'ItemController@auction_price')->name('item.auction_price');
    Route::resource('item', 'ItemController');
    Route::get('vip_items', 'ItemController@vip_auction_items')->name('items.vip');
    Route::get('update_vip/{item_id}', 'ItemController@update_vip')->name('item_vip.update');
    Route::get('item_delivered_to_garage/{item_id}', 'ItemController@item_delivered_to_garage')->name('item_delivered_to_garage.update');

    Route::get('report/add/{item_id}', 'ReportController@add')->name('report.add');
    Route::resource('report', 'ReportController');

    Route::resource('feed_back', 'FeedBackController');
    Route::get('feed_back/{id}/activate', 'FeedBackController@activate')->name('feed_back.activate');

    Route::resource('package', 'PackageController');
    Route::get('auction/{auction}/items', 'AuctionController@items')->name('auction.items');
    Route::resource('auction', 'AuctionController');

    Route::get('notification/admin_notify_type/{admin_notify_type}', 'NotificationController@notifications')->name('notification.admin_notify_type');
    Route::resource('notification', 'NotificationController');

    Route::get('show_single_contact/{id}', 'ContactController@show_single_contact');
    Route::get('single_contact_form/{user_id}/{contact_id}', 'ContactController@single_contact_form')->name('contact.form');
    Route::post('send_single_contact', 'ContactController@send_single_contact')->name('contact.send');
    Route::resource('contact', 'ContactController');
    Route::get('send_single_notify/{receiver_id}/{note}', 'ContactController@send_single_notify');

    Route::get('transfer/reject/{id}', 'TransferController@reject')->name('transfer.reject');
    Route::get('transfer/accept/{id}', 'TransferController@accept')->name('transfer.accept');
    Route::resource('transfer', 'TransferController');

    Route::get('refund/reject/{id}', 'RefundController@reject')->name('refund.reject');
    Route::get('refund/accept/{id}', 'RefundController@accept')->name('refund.accept');
    Route::resource('refund', 'RefundController');

});
Auth::routes();
Route::get('/', function (){
    return view('index');
})->name('home');

