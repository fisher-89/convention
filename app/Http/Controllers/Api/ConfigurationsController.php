<?php

namespace App\Http\Controllers\Api;

use App\Events\ConfigurationSave;
use App\Events\ConfigurationUpdate;
use App\Events\DrawContinue;
use App\Events\DrawStart;
use App\Events\DrawStop;
use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Configuration;
use App\Models\Sign;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConfigurationsController extends Controller
{
    /**
     * 获取配置
     * @return int
     */
    public function index()
    {
        $data = Configuration::with([
            'award',
            'winners' => function ($query) {
                $query->where('is_receive', 1);
            },
            'winners.sign',
        ])
            ->orderBy('round', 'asc')
            ->get();
        return response()->json($data, 200);
    }

    /**
     * 配置提交
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $maxRound = Configuration::max('round');

        $round = $maxRound ? (++$maxRound) : 1;

        $request->offsetSet('round', $round);
        $data = Configuration::create($request->input());
        broadcast(new ConfigurationSave($data->load('award')->toArray()));
        return response()->json($data, 201);
    }

    /**
     * 修改配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->validateRequest($request);
        $round = $request->route('round');
        $config = Configuration::where('round',$round)->firstOrFail();
        abort_if($config->winners->count()>0,400,'本轮已经有中奖用户了不能进行编辑操作');
        $config->update($request->input());
        broadcast(new ConfigurationUpdate($config->load('award')->toArray()));
        return response()->json($config,201);
    }

    protected function validateRequest($request)
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
    }
    /**
     * 开始抽奖
     * @param Request $request
     */
    public function start(Request $request)
    {
        $round = $request->query('round');
        $config = Configuration::with('award')->where('round', $round)->first();
        $config->is_progress = 1;
        $config->save();
        $data = $config->toArray();

        $users = $this->getDrawUsers();
        broadcast(new DrawStart($data, $users));
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
        $config = Configuration::with([
            'award',
            'winners' => function ($query) {
                $query->where('is_receive', 1);
            }
        ])->where('round', $round)->first();
        $persions = ($config->winners->count() > 0) ? ($config->persions - $config->winners->count()) : $config->persions;
        $winnerOpenid = Winner::pluck('openid')->all();
        $winnerUsers = Sign::whereNotIn('openid', $winnerOpenid)->inRandomOrder()->limit($persions)->get();
        // 中奖用户存入数据库
        $winnerUsers->map(function ($user) use ($round) {
            $data['openid'] = $user->openid;
            $data['round'] = $round;
            Winner::create($data);
        });
        $config->is_progress = 0;
        $config->save();
        broadcast(new DrawStop($config->toArray(), $winnerUsers->toArray()));
        return response()->json($winnerUsers, 200);

    }

    /**
     * 继续抽奖
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function continueDraw(Request $request)
    {
        $round = $request->query('round');
        $config = Configuration::with('award')
        ->withCount(['winners' => function ($query) {
            $query->where('is_receive', 1);
        }])->where('round', $round)->first();
        abort_if($config->winners_count == $config->persions, 400, '本轮不能继续抽奖了，请重新开启');
        $config->is_progress = 1;
        $config->save();
        $config->continue = ($config->persions - $config->winners_count);
        $data = $config->toArray();

        $users = $this->getDrawUsers();
        broadcast(new DrawContinue($data, $users));
        return response()->json($data, 200);
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

    /**
     * 获取奖品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAward()
    {
        $data = Award::get();
        return response()->json($data,200);
    }

    /**
     * 大屏获取最新配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewConfiguration()
    {
        $maxRound = Configuration::max('round');
        $data = Configuration::with([
            'award',
            'winners' => function ($query) {
                $query->where('is_receive', 1);
            },
            'winners.sign',
        ])
            ->where('round',$maxRound)
            ->firstOrFail();
        $users = $this->getDrawUsers();
        return response()->json([
            'data'=>$data,
            'users'=>$users,
        ], 200);
    }
}
