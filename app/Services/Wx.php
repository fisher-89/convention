<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22/022
 * Time: 16:51
 */

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class Wx
{

    public function getUserInfo(string $openid){
        $token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN';
        $result = $this->curl($url);
        return $result;
    }

    public function getAccessToken()
    {
        $accessToken = Cache::remember('access_token', 115, function () {
            return $this->httpRequestAccessToken();
        });
       return $accessToken;
    }

    protected function httpRequestAccessToken()
    {
        $appId = config('wechat.official_account.default.app_id');
        $secret =  config('wechat.official_account.default.secret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $secret;
        $result = $this->curl($url);
        return $result['access_token'];
    }

    private function curl($url, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}