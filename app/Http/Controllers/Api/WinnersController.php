<?php

namespace App\Http\Controllers\Api;

use App\Events\WinnerAbandon;
use App\Events\WinnerSubmit;
use App\Models\Configuration;
use App\Models\Winner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class WinnersController extends Controller
{
    /**
     * 中奖列表
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $round = request()->query('round');
        $data = Winner::with('sign')
            ->where('is_receive',1)
            ->when($round, function ($query) use ($round) {
                return $query->where('round', $round);
            })
            ->get();
        return response()->json($data, 200);
    }

    /**
     * 中奖提交
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = [
          'round'=>'抽奖轮数',
          'openid'=>'中奖用户'
        ];
        $request->validate([
            'round'=>[
                'required',
                'integer',
                Rule::exists('configurations')
            ],
            'openid'=>[
                'array'
            ]
        ],[],$message);

        $round = $request->input('round');
        $response = [];
        foreach ($request->input('openid') as $openid){
            $data = ['openid'=>$openid,'round'=>$round];
            $db = Winner::create($data);
            array_push($response,$db->toArray());
        }
        broadcast(new WinnerSubmit($response));
        return response()->json($response, 201);
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

    /**
     * 弃奖
     * @param Request $request
     */
    public function abandonPrize(Request $request)
    {
        $winner = Winner::where($request->input())->first();
        $winner->is_receive = 0;
        $winner->save();
        broadcast(new WinnerAbandon($winner->toArray()));
        return response()->json($winner, 201);
    }
}
