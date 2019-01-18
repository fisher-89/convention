<?php

namespace App\Http\Controllers\Api;

use App\Models\Sign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class SignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wechatUser = session('wechat.auth_user.default');
        dd($wechatUser);
        $data['openid'] = $wechatUser->getId();
        $data['nickname'] = $wechatUser->getName();
        $data['avatar'] = $wechatUser->avatar;
        $data['sex'] = $wechatUser->original['sex'];
        $data['name'] = '刘勇';
        $data['moblie'] = 15882158753;
        $response = Sign::create($data);
        dd($response);
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
                'numeric',
                'regex:/^1[23456789]\d{9}$/',
                Rule::unique('signs', 'mobile'),
            ],
        ], [], $message);
        $wechatUser = session('wechat.auth_user.default');
        $data['openid'] = $wechatUser->getId();
        $data['nickname'] = $wechatUser->getName();
        $data['avatar'] = $wechatUser->avatar;
        $data['sex'] = $wechatUser->original['sex'];
        $data['name'] = $request->input('name');
        $data['moblie'] = $request->input('mobile');
        Sign::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
