<?php

namespace App\Http\Controllers\Api;

use App\Services\Wx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeChatController extends Controller
{
    protected $wx;
    public function __construct(Wx $wx)
    {
        $this->wx = $wx;
    }

    /**
     * 获取openid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOpenid(Request $request)
    {
        $request->validate([
            'code'=>[
                'required',
                'string'
            ]
        ],[],[
            'code'=>'微信授权code'
        ]);
        $code = $request->input('code');
        $openid = $this->wx->getOpenid($code);
        $data = [
            'openid'=>$openid,
        ];
        return response()->json($data,201);
    }
}
