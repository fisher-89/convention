<?php

namespace App\Http\Controllers\Api;

use App\Models\Sign;
use App\Models\Winner;
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
        $winnerOpenid = Winner::pluck('openid')->all();
        $pageSize = 2;
        $startPage = $this->getRandomStartPage($winnerOpenid, $pageSize);
        $data = Sign::whereNotIn('openid', $winnerOpenid)->orderBy('created_at', 'asc')->skip($startPage)->take($pageSize)->get();
        return response()->json($data, 200);
    }

    /**
     * 获取随机开始页码
     * @param array $winnerOpenid
     */
    protected function getRandomStartPage(array $winnerOpenid, int $pageSize)
    {
        $signCount = Sign::whereNotIn('openid', $winnerOpenid)->count();
        $totalPage = intval(ceil($signCount / $pageSize));
        $totalPage = ($totalPage > 0) ? $totalPage : 1;
        //当前页随机
        $page = rand(1, $totalPage);
        $startPage = ($page - 1) * $pageSize;
        return $startPage;
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
        $signCount = Sign::where('openid', $wechatUser->getId())->count();
        abort_if($signCount, 400, '你已经签到过了');
        $data['openid'] = $wechatUser->getId();
        $data['nickname'] = $wechatUser->getName();
        $data['avatar'] = $wechatUser->avatar;
        $data['sex'] = $wechatUser->original['sex'];
        $data['name'] = $request->input('name');
        $data['mobile'] = $request->input('mobile');
        $data = Sign::create($data);
        return response()->json($data, 201);
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
