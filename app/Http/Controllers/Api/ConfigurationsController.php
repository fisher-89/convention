<?php

namespace App\Http\Controllers\Api;

use App\Events\DrawContinue;
use App\Events\DrawStart;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConfigurationsController extends Controller
{
    /**
     * 获取最新的配置
     * @return int
     */
    public function index()
    {
        $maxRound = Configuration::max('round');
        $data = Configuration::with('winners.sign')->where('round', $maxRound)->first();
        return $data;
    }

    /**
     * 配置提交
     * @param Request $request
     */
    public function store(Request $request)
    {
        $message = [
            'award_id' => '奖品',
            'persions' => '抽奖人数'
        ];

        $request->validate([
            'award_id' => [
                'integer',
                'required',
                Rule::exists('awards', 'id')
            ],
            'persions' => [
                'required',
                'integer',
                'max:255'
            ]
        ], [], $message);
        $maxRound = Configuration::max('round');

        $round = $maxRound ? (++$maxRound) : 1;

        $request->offsetSet('round',$round);
        $data = Configuration::create($request->input());
        return response()->json($data, 201);
    }

    /**
     * 开始抽奖
     * @param Request $request
     */
    public function start(Request $request)
    {
        $round = $request->query('round');
        $config = Configuration::where('round', $round)->first();
        $data = $config->toArray();
        broadcast(new DrawStart($data));
        return response()->json($data, 200);
    }

    /**
     * 继续抽奖
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function continueDraw(Request $request)
    {
        $round = $request->query('round');
        $config = Configuration::withCount('winners')->where('round', $round)->first();
        abort_if($config->winners_count == $config->persions, 400, '本轮不能继续抽奖了，请重新开启');
        $config->continue = ($config->persions - $config->winners_count);
        $data = $config->toArray();
        broadcast(new DrawContinue($data));
        return response()->json($data, 200);
    }
}
