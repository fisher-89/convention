<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
        $app->server->push(function ($message) {
            return "欢迎关注！";
        });

        return $app->server->serve();
    }

    public function checkSign()
    {
        $redirectUri = 'http://cs.xigemall.com/api/sign';
        if(Session::has('wechat_user')){
            return redirect($redirectUri);
        }else{
            $appId = config('wechat.official_account.default.app_id');
            $query = [
                'appid' => $appId,
                'redirect_uri' => urlencode($redirectUri),
                'response_type'=>'code',
                'scope'=>'snsapi_userinfo',
                'state'=>'STATE',
            ];
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($query) . '#wechat_redirect';
            return redirect($url);
        }
    }
}
