<?php

namespace Hanoivip\Ddd2\Controllers;
use Hanoivip\Ddd2\Services\DddAuthen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;


class Ddd2 extends Controller
{
    private $auth;
    
    public function __construct(DddAuthen $auth)
    {
        $this->auth = $auth;
    }
    
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        Log::debug("Ddd2 user is logining {$username}");
        
        $accessToken = $this->auth->authen($username, $password);
        if (!empty($accessToken))
        {
            Cookie::queue(Cookie::make('ddd2_token',  $accessToken));
            return view('hanoivip::landing');
        }
        else
            return view('hanoivip::login-fail');
    }
}