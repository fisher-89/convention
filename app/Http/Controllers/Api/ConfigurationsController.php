<?php

namespace App\Http\Controllers\Api;

use App\Events\DrawContinue;
use App\Events\DrawStart;
use App\Events\DrawStop;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Sign;
use App\Models\Winner;
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
        return response()->json($data,200);
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

        $users = $this->getDrawUsers();
        broadcast(new DrawStart($data,$users));
        return response()->json($data, 200);
    }

    /**
     * 停止抽奖
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(Request $request)
    {
        $round = $request->query('round');
        $config = Configuration::where('round', $round)->first();
        $persions = $config->persions;
        $winnerOpenid = Winner::pluck('openid')->all();
        $winnerUsers = Sign::whereNotIn('openid', $winnerOpenid)->inRandomOrder()->limit($persions)->get();
        // 中奖用户存入数据库
        $winnerUsers->map(function($user)use($round){
            $data['openid'] = $user->openid;
            $data['round'] = $round;
            Winner::create($data);
        });
        broadcast(new DrawStop($config->toArray(),$winnerUsers->toArray()));
        return response()->json($winnerUsers,200);

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

        $users = $this->getDrawUsers();
        broadcast(new DrawContinue($data,$users));
        return response()->json($users, 200);
    }

    /**
     * 获取抽奖用户
     * @return mixed
     */
    protected function getDrawUsers()
    {
        $winnerOpenid = Winner::pluck('openid')->all();
        $users = Sign::whereNotIn('openid', $winnerOpenid)->inRandomOrder()->limit(200)->get();
        return $users->toArray();
    }
}
