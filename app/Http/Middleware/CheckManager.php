<?php

namespace App\Http\Middleware;

use App\Models\Manager;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckManager
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
        $managerSn = Manager::pluck('staff_sn')->all();
        if(!in_array(Auth::id(),$managerSn)){
            abort(403,'你无后台操作权限');
        }
        return $next($request);
    }
}
