<?php

namespace App\Http\Controllers;

use App\Models\Sign;
use Illuminate\Http\Request;
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
        $wechatUser = session('wechat.oauth_user.default');
        $data = Sign::where('openid',$wechatUser->getId())->first();
        if($data)
            return view('show',$data);
        return view('sign');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
        Sign::create($data);
        return redirect('/sign');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
