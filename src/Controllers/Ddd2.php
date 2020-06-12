<?php
namespace Hanoivip\Ddd2\Controllers;

use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;


class Ddd2 extends Controller
{
    private $auth;
    
    public function __construct(IDddAuthen $auth)
    {
        $this->auth = $auth;
    }
    
    public function login(Request $request)
    {
        if (Auth::check())
            return redirect('/');
        return view('hanoivip::auth.login');
    }
    
    public function doLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        Log::debug("Ddd2 user is logining {$username}");
        try 
        {
            $accessToken = $this->auth->authen($username, $password);
            if (!empty($accessToken))
            {
                if ($request->ajax())
                {
                    return response()->json(['error'=>0, 'message'=>'login success', 'data'=>['token' => $accessToken, 'expires' => Carbon::now()->timestamp]]);   
                }
                else 
                {
                    Cookie::queue(Cookie::make('access_token',  $accessToken));
                    return view('hanoivip::landing');
                }
            }
            else
            {
                if ($request->ajax())
                    return response()->json(['error'=>1, 'message'=>'Đăng nhập thất bại, kiểm tra tài khoản và mật khẩu', 'data'=>[]]);
                else
                    return view('hanoivip::auth.login', ['error' => 'Đăng nhập thất bại, kiểm tra tài khoản và mật khẩu']);
            }
        } 
        catch (Exception $e) 
        {
            Log::error("Ddd2 login ex:" . $e->getMessage());
            if ($request->ajax())
                return response()->json(['error'=>2, 'message'=>'Ddd2 login exception!', 'data'=>[]]);
            else
                return view('hanoivip::auth.login-exception');
        }
        
    }
    
    public function logout(Request $request)
    {
        if (!Auth::check())
        {
            if ($request->ajax())
                return response()->json(['error'=>1, 'message'=>'not login yet', 'data'=>[]]);
            else
                return redirect('/');
        }
        Cookie::queue(Cookie::forget('access_token'));
        Cookie::queue(Cookie::forget('laravel_session'));
        if ($request->ajax())
            return response()->json(['error'=>0, 'message'=>'logout success', 'data'=>[]]);
        else
            return view('hanoivip::landing');
    }
    
    public function onLogout(Request $request)
    {
        return view('hanoivip::landing');
    }
    
    public function register(Request $request)
    {
        return view('hanoivip::auth.register');
    }
    
    public function doRegister(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $result = $this->auth->createUser($username, $password);
        if ($result === true)
            return response()->json(['error'=>0, 'message' => 'registration success', 'data' => []]);
        else
        {
            Log::error('Registration error: ' . $result);
            return response()->json(['error'=>1, 'message'=> 'registration fail', 'data'=>[]]);
        }
    }
    
    public function doGetInfo(Request $request)
    {
        if (!Auth::check())
        {
            return response()->json(['error'=>1, 'message'=>'token invalid', 'data'=>[]]);
        }
        $token = $request->input('access_token');
        $user = $this->auth->getUserByToken($token);
        return response()->json(['error'=>0, 'message'=>'info success', 'data'=>['name' => $user['user_name'], 'email' => $user['email'], 'create_time' => $user['create_time'], 'channel' => $user['channel']]]);
    }
    
    public function forgotPass(Request $request)
    {
        return view('hanoivip::auth.passwords.reset');
    }
}