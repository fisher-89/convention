<?php

namespace App\Http\Controllers\Api;

use App\Models\Winner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class WinnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $round = request()->query('round');
        $data = Winner::with('sign', 'award')
            ->where('is_receive',1)
            ->when($round, function ($query) use ($round) {
                return $query->where('round', $round);
            })
            ->get();
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
        $data = $request->input();
        data_fill($data, '*.created_at', date('Y-m-d H:i:s'));
        Winner::insert($data);
        return response()->json([], 201);
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
        return response()->json($winner, 201);
    }
}
