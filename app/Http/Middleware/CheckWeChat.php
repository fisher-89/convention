<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckWeChat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Session::has('wechat_user')){
            $url = "http://cs.xigemall.com/code";
            return redirect($url);
        }
        return $next($request);
    }
}
