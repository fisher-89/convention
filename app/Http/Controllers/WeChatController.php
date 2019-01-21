<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Log;

class WeChatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
//        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注！";
        });

        return $app->server->serve();
    }

    public function show()
    {
//        $openPlatform = app('wechat.open_platform');
//        $openPlatform = Factory::openPlatform(config('wechat.open_platform.default'));
//        $openPlatform->getPreAuthorizationUrl(route('callback')); // 传入回调URI即可

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx928eacf7fdbd4e0d&redirect_uri='.urlencode(route('callback')).'&response_type=code&scope=snsapi_userinfo';

          //初始化
      $curl = curl_init();
      //设置抓取的url
      curl_setopt($curl, CURLOPT_URL, $url);
      //设置头文件的信息作为数据流输出
      curl_setopt($curl, CURLOPT_HEADER, 1);
      //设置获取的信息以文件流的形式返回，而不是直接输出。
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      //执行命令
     $data = curl_exec($curl);
     //关闭URL请求
     curl_close($curl);
     //显示获得的数据
//     return $data;
    }

    public function callback()
    {
        dd('callback',request()->all());
    }
}
