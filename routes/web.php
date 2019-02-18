<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::any('/wechat', 'WeChatController@serve');

Route::middleware(['wechat.oauth:snsapi_userinfo'])->group(function(){
//    $redirectUrl = 'http://cs.xigemall.com/checkin/checkin.html';
//    Route::redirect('/sign',$redirectUrl,301);
//    Route::resource('/sign','SignsController');
});

Route::get('/code','WeChatController@getCode');

//Route::middleware('check_wechat')->group(function(){
    $redirectUrl = 'http://112.74.177.132:8107/checkin/index.html';
    Route::redirect('/sign',$redirectUrl,301);
//});