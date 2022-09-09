<?php

namespace Hanoivip\Ddd2\Middlewares;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Closure;
use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Support\Facades\Cookie;

class Relogin
{
    private $auth;
    public function __construct(IDddAuthen $auth)
    {
        $this->auth=$auth;
    }
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check())
        {
            $key = 'LastLogin_' . Auth::user()->getAuthIdentifier();
            $interval = 60;//s
            if (Cache::has($key) &&
                Carbon::now()->diffInSeconds(Cache::get($key)) > $interval)
            {
                $token = Cookie::get('access_token');
                $this->auth->logout($token);
                $current = $request->getRequestUri();
                return response()->redirectToRoute('login', ['redirect' => $current]);
            }
            else
            {
                Log::debug("Relogin middeware .. " . Carbon::now()->timestamp . " & " . Cache::get($key)->timestamp);
            }
        }
        return $next($request);
    }
}
