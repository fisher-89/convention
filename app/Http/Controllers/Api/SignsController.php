<?php

namespace App\Http\Controllers\Api;

use App\Models\Sign;
use App\Models\Winner;
use App\Services\Wx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
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
        $data = Sign::get();
        return response()->json($data,200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(!Cache::has($request->input('openid')),400,'当前openid不存在');
        $message = [
            'openid'=>'微信号',
            'name' => '姓名',
            'mobile' => '手机'
        ];

        $request->validate([
            'openid' => [
                'required',
                'string',
                Rule::unique('signs'),
            ],
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

        $data = Cache::get($request->input('openid'));
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
        $data = Sign::where('openid', $openid)->firstOrFail();
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $openid)
    {
        $message = [
          'hotel_name'=>'酒店名称',
          'hotel_num'=>'酒店房号',
          'idcard'=>'身份证',
          'start_time'=>'入住开始时间',
          'end_time'=>'入住结束时间',
          'money'=>'酒店费用',
        ];
        $request->validate([
            'hotel_name'=>[
                'string',
                'max:50'
            ],
            'hotel_num'=>[
                'string',
                'max:30'
            ],
            'idcard'=>[
                'string',
                'max:20'
            ],
            'start_time'=>[
                'date',
                'nullable'
            ],
            'end_time'=>[
                'date',
                'nullable'
            ],
            'money'=>[
                'string'
            ]
        ],[],$message);
        $data = Sign::where('openid',$openid)->firstOrFail();
        $data->hotel_name = $request->input('hotel_name');
        $data->hotel_num = $request->input('hotel_num');
        $data->idcard = $request->input('idcard');
        $data->start_time = $request->input('start_time');
        $data->end_time = $request->input('end_time');
        $data->money = $request->input('money');
        $data->save();
        return response()->json($data,201);

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
