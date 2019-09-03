<?php

namespace Hanoivip\Ddd2\Controllers;
use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Exception;


class Ddd2 extends Controller
{
    private $auth;
    
    public function __construct(IDddAuthen $auth)
    {
        $this->auth = $auth;
    }
    
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        Log::debug("Ddd2 user is logining {$username}");
        try 
        {
            $accessToken = $this->auth->authen($username, $password);
            if (!empty($accessToken))
            {
                Cookie::queue(Cookie::make('ddd2_token',  $accessToken));
                return view('hanoivip::landing');
            }
            else
                return view('hanoivip::auth.login', ['error' => 'Đăng nhập thất bại, kiểm tra tài khoản và mật khẩu']);
        } 
        catch (Exception $e) 
        {
            Log::error("Ddd2 login ex:" . $e->getMessage());
            return view('hanoivip::auth.login-exception');
        }
        
    }
}