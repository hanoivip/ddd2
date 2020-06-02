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
                    return response()->json(['error'=>0, 'message'=>'login success', 'data'=>['token' => $accessToken, 'expires' => Carbon::now()->timestamp]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');   
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
                    return response()->json(['error'=>1, 'message'=>'Đăng nhập thất bại, kiểm tra tài khoản và mật khẩu', 'data'=>[]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                else
                    return view('hanoivip::auth.login', ['error' => 'Đăng nhập thất bại, kiểm tra tài khoản và mật khẩu']);
            }
        } 
        catch (Exception $e) 
        {
            Log::error("Ddd2 login ex:" . $e->getMessage());
            if ($request->ajax())
                return response()->json(['error'=>2, 'message'=>'Ddd2 login exception!', 'data'=>[]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            else
                return view('hanoivip::auth.login-exception');
        }
        
    }
    
    public function logout(Request $request)
    {
        if (!Auth::check())
            return redirect('/');
        Cookie::queue(Cookie::forget('access_token'));
        Cookie::queue(Cookie::forget('laravel_session'));
        if ($request->ajax())
            return response()->json(['error'=>0, 'message'=>'logout success', 'data'=>[]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
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
        return response()->json(['error'=>1, 'message'=>'registration from app only!', 'data'=>[]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    
    public function doGetInfo(Request $request)
    {
        if (!Auth::check())
        {
            return response()->json(['error'=>1, 'message'=>'token invalid', 'data'=>[]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
        $user = Auth::user();
        return response()->json(['error'=>0, 'message'=>'info success', 'data'=>['name' => $user->getAuthIdentifierName(), 'email' => $user['email']]])->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    
    public function forgotPass(Request $request)
    {
        return view('hanoivip::auth.passwords.reset');
    }
}