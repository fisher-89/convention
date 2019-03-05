<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22/022
 * Time: 16:51
 */

namespace App\Services;

use App\Models\Sign;
use Illuminate\Support\Facades\Cache;

class Wx
{
    /**
     * 微信用户信息存入缓存
     * @return string
     */
    public function wechatUserInfoToCache()
    {
        $wechatUser = session('wechat.oauth_user.default');
        $openId = $wechatUser->getId();
        $data['openid'] = $openId;
        $data['nickname'] = $wechatUser->getName();
        $data['avatar'] = $wechatUser->avatar;
        $data['sex'] = $wechatUser->original['sex'];

        if (!Cache::has($openId)) {
            Cache::forever($openId, $data);
        }

        // 检测是否签到过
        $data = Cache::get($openId);
        $sign = Sign::where('openid', $openId)->first();
        $clear = false;
        if ((!array_has($data, 'name')) && $sign) {
            $data['name'] = $sign->name;
            $data['mobile'] = $sign->mobile;
            $data['number'] = $sign->number;
            Cache::forever($openId, $data);
        } elseif (!array_has($data, 'name')) {
            $clear = true;
        }

        return ['openid' => $openId, 'clearCache' => $clear];
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