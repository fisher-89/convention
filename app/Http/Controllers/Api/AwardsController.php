<?php

namespace App\Http\Controllers\Api;

use App\Models\Award;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AwardsController extends Controller
{
    /**
     * 获取奖品列表
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Award::get();
        return response()->json($data,200);
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
          'name'=>'奖品名称',
          'url'=>'图片',
        ];
        $request->validate([
            'name'=>[
                'required',
                'string',
                'max:20',
                Rule::unique('awards')
            ],
            'url'=>[
                'required',
                'url',
                'string',
                'max:255'
            ]
        ],[],$message);
        $url = $request->input('url');
        $newUrl = $url ? str_after($url, config('app.url') . '/storage') : $url;
        $request->offsetSet('url', $newUrl);
        $data = Award::create($request->input());
        return response()->json($data,201);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>[
                'required',
                'string',
                'max:20',
                Rule::unique('awards')->ignore($id)
            ],
            'url'=>[
                'required',
                'url',
                'string',
                'max:255'
            ]
        ],[],[]);
        $data = Award::findOrFail($id);
        $url = $request->input('url');
        $newUrl = $url ? str_after($url, config('app.url') . '/storage') : $url;
        $request->offsetSet('url', $newUrl);
        $data->update($request->input());
        return response()->json($data,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Award::findOrFail($id);
        $data->delete();
        return response('',204);
    }

    /**
     * 上传奖品
     * @param Request $request
     */
    public function uploadAward(Request $request)
    {
        $message = [
            'url' => '奖品图片',
        ];
        $request->validate([
            'url' => [
                'required',
                'file',
            ],
        ], [], $message);

        $file = $request->file('url');
        // 扩展名
        $extension = $file->getClientOriginalExtension();
        $fileName = date('YmdHis') . '-' . str_random(3) . '.' . $extension;
        $idcardPath = $request->url->storeAs('award', $fileName, 'public');
        $path = config('app.url') . '/storage/' . $idcardPath;
        return response()->json($path, 201);
    }
}
