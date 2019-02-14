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
    /**
     * 通过code换取网页授权access_token 存入缓存，返回openid
     * @param string $code
     * @return mixed
     */
    public function getOpenid(string $code)
    {
        $data = $this->setWebAccessTokenToCache($code);
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * 设置token
     * @param string $code
     * @return mixed
     */
    protected function setWebAccessTokenToCache(string $code)
    {
        $appId = config('wechat.official_account.default.app_id');
        $secret = config('wechat.official_account.default.secret');
        $grantType = 'authorization_code';
        $query = [
            'appid' => $appId,
            'secret' => $secret,
            'code' => $code,
            'grant_type' => $grantType
        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($query);
        $result = $this->curl($url);
        if (!array_has($result, 'access_token')) {
            abort(400, '获取access_token失败');
        }
        $openid = $result['openid'];
        $result['time'] = time() + 7100;
        Cache::forever($openid,$result);
        return $result;
    }

    /**
     * 检测微信用户token
     * @param string $openid
     * @return string
     */
    public function checkWebAccessToken(string $openid)
    {
        abort_if(!Cache::has($openid),400,'当前openid不存在');
        $data =  Cache::get($openid);
        if(time() > $data['time']){
            $result = $this->refreshWebAccessToken($data['refresh_token']);
            return $result['openid'];
        }
        return $openid;
    }
    /**
     * 刷新access_token
     * @param string $refreshToken
     * @return mixed
     */
    protected function refreshWebAccessToken( string $refreshToken){
        $appId = config('wechat.official_account.default.app_id');
        $grantType = 'refresh_token';
        $query = [
            'appid' => $appId,
            'grant_type' => $grantType,
            'refresh_token' => $refreshToken,

        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?' . http_build_query($query);
        $result = $this->curl($url);
        if (!array_has($result, 'access_token')) {
            abort(400, '刷新access_token失败');
        }
        $result['time'] = time() + 7100;
        Cache::forever($result['openid'],$result);
        return $result;
    }

    /**
     * 获取网页授权accessToken
     * @param string $openid
     * @return mixed
     */
    protected function getWebAccessToken(string $openid)
    {
        $data = Cache::get($openid);
        return $data['access_token'];
    }

    public function getUserInfo(string $openid)
    {
        $accessToken = $this->getWebAccessToken($openid);
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';
        $result = $this->curl($url);
        abort_if(!array_has($result,'openid'),400,'通过openid获取用户信息失败');
        return $result;
    }


//    public function getUserInfo(string $openid)
//    {
//        $token = $this->getAccessToken();
//        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN';
//        $result = $this->curl($url);
//        return $result;
//    }

//    public function getAccessToken()
//    {
//        $accessToken = Cache::remember('access_token', 115, function () {
//            return $this->httpRequestAccessToken();
//        });
//        return $accessToken;
//    }
//
//    protected function httpRequestAccessToken()
//    {
//        $appId = config('wechat.official_account.default.app_id');
//        $secret = config('wechat.official_account.default.secret');
//        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $secret;
//        $result = $this->curl($url);
//        return $result['access_token'];
//    }

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