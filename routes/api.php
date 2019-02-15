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

// 获取网页授权openid
Route::post('openid','Api\WeChatController@getOpenid');

// 签到
Route::apiResource('sign','Api\SignsController');

// 获取最新的配置
Route::get('configuration','Api\ConfigurationsController@index');
// 配置提交
Route::post('configuration','Api\ConfigurationsController@store');
// 配置修改
Route::put('configuration/{round}','Api\ConfigurationsController@update');
// 开始抽奖
Route::get('start','Api\ConfigurationsController@start');
// 停止抽奖
Route::get('stop','Api\ConfigurationsController@stop');
// 继续抽奖
Route::get('continue','Api\ConfigurationsController@continueDraw');


// 中奖
Route::apiResource('winner','Api\WinnersController');
//弃奖
Route::patch('abandon_prize','Api\WinnersController@abandonPrize');

