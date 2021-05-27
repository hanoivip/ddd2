<?php
namespace Hanoivip\Ddd2\Controllers;

use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
    
    private function validateLogin($request)
    {
        return Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:6', 'max:32'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }
    
    public function doLogin(Request $request)
    {
        $validator = $this->validateLogin($request);
        if ($validator->fails())
        {
            if ($request->expectsJson())
            {
                return response()->json(['error'=>1, 'message' => 'Thông tin đăng ký không hợp lệ!', 'data' => $validator->errors()]);
            }
            else
            {
                return redirect()->route('login')->withErrors($validator)->withInput();
            }
        }
        $username = $request->input('username');
        $password = $request->input('password');
        Log::debug("Ddd2 user is logining {$username}");
        try 
        {
            $accessToken = $this->auth->authen($username, $password);
            if (!empty($accessToken))
            {
                if ($request->expectsJson())
                {
                    $user = $this->auth->getUserByToken($accessToken);
                    return response()->json(['error'=>0, 'message'=>'login success', 
                        'data'=>['token' => $accessToken, 'expires' => $user->expires->timestamp, 
                            'app_user_id' => $user->getAuthIdentifier()]]);   
                }
                else 
                {
                    Cookie::queue(Cookie::make('access_token',  $accessToken));
                    return view('hanoivip::landing');
                }
            }
            else
            {
                if ($request->expectsJson())
                    return response()->json(['error'=>1, 'message'=> __('hanoivip::auth.failed'), 'data'=>[]]);
                else
                    return view('hanoivip::auth.login', ['error' => __('hanoivip::auth.failed')]);
            }
        } 
        catch (Exception $e) 
        {
            Log::error("Ddd2 login ex:" . $e->getMessage());
            if ($request->expectsJson())
                return response()->json(['error'=>2, 'message'=>'Ddd2 login exception!', 'data'=>[]]);
            else
                return view('hanoivip::auth.login-exception');
        }
        
    }
    
    public function logout(Request $request)
    {
        if (!Auth::check())
        {
            if ($request->expectsJson())
                return response()->json(['error'=>1, 'message'=>'not login yet', 'data'=>[]]);
            else
                return redirect('/');
        }
        Cookie::queue(Cookie::forget('access_token'));
        Cookie::queue(Cookie::forget('laravel_session'));
        if ($request->expectsJson())
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
    
    private function validateRegister($request)
    {
        return Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:6', 'max:32'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }
    
    public function doRegister(Request $request)
    {
        $validator = $this->validateRegister($request);
        if ($validator->fails()) 
        {
            if ($request->expectsJson())
            {
                return response()->json(['error'=>1, 'message' => 'Thông tin đăng ký không hợp lệ!', 'data' => $validator->errors()]);
            }
            else
            {
                return redirect()->route('register')->withErrors($validator)->withInput();
            }
        }
        $username = $request->input('username');
        $password = $request->input('password');
        try {
            $result = $this->auth->createUser($username, $password);
            if ($result === true) {
                if ($request->expectsJson()) {
                    //auto login for this client
                    $accessToken = $this->auth->authen($username, $password);
                    $user = $this->auth->getUserByToken($accessToken);
                    return response()->json(['error'=>0, 'message' => __('hanoivip::auth.success'), 
                        'data' => ['token' => $accessToken, 'expires' => $user->expires->timestamp,  
                            'app_user_id' => $user->getAuthIdentifier()]]);
                }
                else {
                    return view('hanoivip::auth.login', ['error' => __('hanoivip::auth.success')]);
                }
            }   
            else {
                if ($request->expectsJson()) {
                    return response()->json(['error'=>2, 'message' => $result, 'data' => []]);
                }
                else {
                    return view('hanoivip::auth.register', ['error' => $result ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Registration exception: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error'=>3, 'message' => "Registration exception", 'data' => []]);
            }
            else {
                return view('hanoivip::auth.register', ['error' => "Registration exception" ]);
            }
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
        return response()->json(['error'=>0, 'message'=>'info success', 
            'data'=>['name' => $user->getAuthIdentifierName(), 'email' => $user->email, 
                'create_time' => $user->createTime, 'app_user_id' => $user->getAuthIdentifier()]]);
    }
    
    public function forgotPass(Request $request)
    {
        return view('hanoivip::auth.passwords.reset');
    }
}