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

// 签到提交
Route::post('sign', 'Api\SignsController@store');
// 签到详情
Route::get('sign/{openid}', 'Api\SignsController@show');

// 检测用户登陆
Route::get('check/{openid}','Api\SignsController@check');

Route::middleware('auth:api')->group(function () {
    // 签到列表
    Route::get('sign', 'Api\SignsController@index');
    // 签到补充信息
    Route::patch('sign/{openid}', 'Api\SignsController@update');
    // 上传身份证
    Route::post('upload', 'Api\SignsController@upload');

    // 获取配置
    Route::get('configuration', 'Api\ConfigurationsController@index');
    // 配置提交
    Route::post('configuration', 'Api\ConfigurationsController@store');
    // 配置修改
    Route::put('configuration/{round}', 'Api\ConfigurationsController@update');
    // 清空全部配置与中奖信息
    Route::delete('configuration_clear','Api\ConfigurationsController@configurationClear');
    // 开始抽奖
    Route::get('start', 'Api\ConfigurationsController@start');
    // 停止抽奖
    Route::get('stop', 'Api\ConfigurationsController@stop');
    // 继续抽奖
    Route::get('continue', 'Api\ConfigurationsController@continueDraw');

    // 奖品
    Route::apiResource('award', 'Api\AwardsController')->only([
        'index','store','update','destroy'
    ]);
    // 上传奖品
    Route::post('upload_award','Api\AwardsController@uploadAward');

    // 中奖
    Route::apiResource('winner', 'Api\WinnersController')->only([
        'index'
    ]);
    //弃奖
    Route::patch('abandon_prize', 'Api\WinnersController@abandonPrize');
});

// 大屏获取最新配置抽奖
Route::get('new_configuration', 'Api\ConfigurationsController@getNewConfiguration');

