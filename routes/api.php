<?php

use Illuminate\Http\Request;

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

Route::namespace('Api')->middleware(['wechat.oauth'])->group(function(){
//    Route::get('form',);
    //签到提交
    Route::post('sign','SignsController@store');
});
//签到列表
Route::get('sign','Api\SignsController@index');

// 中奖
Route::apiResource('winner','Api\WinnersController');
