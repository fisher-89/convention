<?php

namespace App\Http\Controllers\Api;

use App\Models\Sign;
use App\Models\Winner;
use App\Services\Wx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request)
    {
        $category = $request->query('category');
        $data = Sign::when($category == 'mobile', function ($query) {
            return $query->where('number', 'like', 'CJ%')
                ->orWhere('number', 'like', 'JN%')
                ->orWhere('number', 'like', 'LS%')
                ->orWhere('number', 'like', 'LS%')
                ->orWhere('number', 'like', 'GO%')
                ->orWhere('number', 'like', 'FM%');
        })
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
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
        abort_if(!Cache::has($request->input('openid')), 400, '当前openid不存在');
        $message = [
            'openid' => '微信号',
            'name' => '姓名',
            'mobile' => '手机',
            'number' => '编号',
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
            'number' => [
                'required',
                Rule::unique('signs', 'number'),
                Rule::exists('invites', 'number')->where('name', $request->input('name'))
            ]
        ], [], $message);
        $data = Cache::get($request->input('openid'));
        $data['name'] = $request->input('name');
        $data['mobile'] = $request->input('mobile');
        $data['number'] = $request->input('number');
        $response = Sign::create($data);
        Cache::forever($data['openid'],$data);
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
            'hotel_name' => '酒店名称',
            'hotel_num' => '酒店房号',
            'idcard' => '身份证',
            'start_time' => '入住开始时间',
            'end_time' => '入住结束时间',
            'money' => '酒店费用',
        ];
        $request->validate([
            'hotel_name' => [
                'string',
                'max:50',
                'nullable'
            ],
            'hotel_num' => [
                'string',
                'max:30',
                'nullable'
            ],
            'idcard' => [
                'string',
                'max:255',
                'nullable'
            ],
            'start_time' => [
                'date',
                'nullable'
            ],
            'end_time' => [
                'date',
                'nullable'
            ],
            'money' => [
                'string',
                'nullable'
            ]
        ], [], $message);
        $data = Sign::where('openid', $openid)->firstOrFail();

        // 写入日志
        Log::channel('single')->info($data->update_name . '修改之前');
        Log::channel('single')->info($data->toArray());

        $idcard = $request->input('idcard');
        $newIdcard = $idcard ? str_after($idcard, config('app.url') . '/storage/') : $idcard;
        $request->offsetSet('idcard', $newIdcard);
        $request->offsetSet('update_staff', Auth::id() ?: '');
        $request->offsetSet('update_name', (Auth::user()) ? Auth::user()->realname : '');
        $data->update($request->input());

        Log::channel('single')->info($data->update_name . '修改之后');
        Log::channel('single')->info($data->toArray());
//        $data->hotel_name = $request->input('hotel_name');
//        $data->hotel_num = $request->input('hotel_num');
//        $data->idcard = $request->input('idcard');
//        $data->start_time = $request->input('start_time');
//        $data->end_time = $request->input('end_time');
//        $data->money = $request->input('money');
//        $data->save();
        return response()->json($data, 201);

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

    /**
     * 上传身份证
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $message = [
            'idcard' => '身份证',
        ];
        $request->validate([
            'idcard' => [
                'file',
            ],
        ], [], $message);

        $file = $request->file('idcard');
        // 扩展名
        $extension = $file->getClientOriginalExtension();
        $fileName = date('YmdHis') . '-' . str_random(6) . '.' . $extension;
        $idcardPath = $request->idcard->storeAs('images', $fileName, 'public');
        $path = config('app.url') . '/storage/' . $idcardPath;
        return response()->json($path, 201);
    }

    /**
     * 检测用户登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $openid = $request->route('openid');
        $data = Cache::get($openid);
        return response()->json($data,200);
    }
}
