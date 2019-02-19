<?php

namespace App\Http\Controllers\Api;

use App\Models\Sign;
use App\Models\Winner;
use App\Services\Wx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SignsController extends Controller
{
    protected $wx;

    public function __construct(Wx $wx)
    {
        $this->wx = $wx;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $size = request()->query('size', 200);
        $winnerOpenid = Winner::pluck('openid')->all();
        $data = Sign::whereNotIn('openid', $winnerOpenid)->inRandomOrder()->limit($size)->get();
        return response()->json($data, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $message = [
            'name' => '姓名',
            'mobile' => '手机'
        ];

        $request->validate([
            'name' => [
                'required',
                'between:2,10',
                'string'
            ],
            'mobile' => [
                'required',
                'string',
                'regex:/^1[23456789]\d{9}$/',
                Rule::unique('signs', 'mobile'),
            ],
        ], [], $message);

        $wechatUser = session('wechat.oauth_user.default');
        $openId = $wechatUser->getId();
        $signCount = Sign::where('openid', $openId)->count();
        abort_if($signCount, 400, '你已经签到过了');

        $data['openid'] = $openId;
        $data['nickname'] = $wechatUser->getName();
        $data['avatar'] = $wechatUser->avatar;
        $data['sex'] = $wechatUser->original['sex'];
        $data['name'] = $request->input('name');
        $data['mobile'] = $request->input('mobile');
        $response = Sign::create($data);
        return response()->json($response, 201);
    }

    /**
     * 获取签到用户详情
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($openid)
    {
       $data =  Sign::where('openid',$openid)->firstOrFail();
       return response()->json($data,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
