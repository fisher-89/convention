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

    public function getWebAccessToken(Request $request)
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
        $data = $this->wx->getWebAccessToken($code);
        return response()->json($data,201);
    }
}
