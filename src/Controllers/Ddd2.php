<?php
namespace Hanoivip\Ddd2\Controllers;

use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;
use Hanoivip\User\Facades\DeviceFacade;
use Illuminate\Auth\Events\Login;
use Hanoivip\Ddd2\Requests\LoginRequest;
use Hanoivip\Ddd2\Services\RedirectsUsers;
use Hanoivip\Ddd2\Services\ThrottlesLogins;

class Ddd2 extends Controller
{
    use RedirectsUsers, ThrottlesLogins;
    
    private $auth;
    
    public function __construct(IDddAuthen $auth)
    {
        $this->auth = $auth;
    }
    
    public function login(Request $request)
    {
        return $this->loginWithRedirect($request);
    }
    
    private function loginWithRedirect(Request $request, $data = [])
    {
        $redirect = '';
        if ($request->has('redirect'))
            $redirect = $request->get('redirect');
        $data['redirect'] = $redirect;
        return view('hanoivip::auth.login', $data);
    }
    
    public function doLogin(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) 
        {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request))
        {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    
    protected function attemptLogin(Request $request)
    {
        $device = $request->get('device');
        $username = $request->input('username');
        $password = $request->input('password');
        Log::debug("Ddd2 user is logining {$username}");
        try
        {
            return Auth::attempt(['username' => $username, 'password' => $password, 'device' => $device->deviceId]);
        }
        catch(Exception $ex)
        {
            Log::error("Ddd2 attemp login error: " . $ex->getMessage());
            return false;
        }
    }
    
    private function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        $device = $request->get('device');
        $user = Auth::user();
        $accessToken = Str::random(16);
        DeviceFacade::mapUserDevice($device, $user->getAuthIdentifier(), $accessToken);
        event(new Login("ddd2", $user, true));
        if ($request->expectsJson())
        {
            return response()->json([
                'error'=>0,
                'message'=>'login success',
                'data'=>[
                    'token' => $accessToken,
                    'expires' => Carbon::now()->addDays(30)->timestamp,
                    'app_user_id' => $user->getAuthIdentifier()
                ]]);
        }
        else
        {
            $redirect = $request->get('redirect');
            if (!empty($redirect))
            {
                return response()->redirectTo($redirect);
            }
            return redirect()->route('login-success');
        }
    }
    
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->expectsJson())
        {
            return response()->json(['error'=>1, 'message'=> __('hanoivip.ddd2::auth.failed')]);
        }
        else
        {
            return view('hanoivip::auth.login', ['error_message' => __('hanoivip.ddd2::auth.failed')]);
        }
    }
    
    public function username()
    {
        return 'username';
    }
    
    public function logout(Request $request)
    {
        Auth::guard()->logout();
        
        if ($request->expectsJson())
        {
            return response()->json(['error'=>0, 'message'=>'logout success', 'data'=>[]]);
        }
        else
        {
            $request->session()->invalidate();
            return redirect()->route('logout-success');
        }
    }
    
    public function onLogout(Request $request)
    {
        return view('hanoivip::auth.logout-success');
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
            'captcha' => ['required', 'captcha']
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
        $device = $request->get('device');
        //Log::debug(print_r($device, true));
        $username = $request->input('username');
        $password = $request->input('password');
        try {
            $result = $this->auth->createUser($device->deviceId, $username, $password);
            Log::debug(print_r($result, true));
            if ($result === true) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error'=>0, 
                        'message' => __('hanoivip.ddd2::auth.success'), 
                        'data' => []]);
                }
                else {
                    return redirect()->route('register-success');
                }
            }   
            else {
                if ($request->expectsJson()) {
                    return response()->json(['error'=>2, 'message' => $result, 'data' => []]);
                }
                else {
                    return view('hanoivip::auth.register', ['error_message' => $result ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Registration exception: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error'=>3, 'message' => "Registration exception", 'data' => []]);
            }
            else {
                return view('hanoivip::auth.register', ['error_message' => "Registration exception" ]);
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
		$isGuest = $user->getAuthIdentifierName() == $user->device;
        return response()->json(['error'=>0, 'message'=>'info success', 
            'data'=>['name' => $user->getAuthIdentifierName(), 'email' => $user->email, 
                'create_time' => $user->createTime, 'app_user_id' => $user->getAuthIdentifier(),
				'is_guest' => $isGuest ]]);
    }
    
    public function forgotPass(Request $request)
    {
        return view('hanoivip::auth.passwords.reset');
    }
    
    public function guest(Request $request)
    {
        $deviceId = $request->input('device');
        if (empty($deviceId))
        {
            return response()->json(['error' => 1, 'message' => 'device id null', 'data' => []]);
        }
        // process register
        $username = $deviceId;
        $password = $deviceId;
        try
        {
            $accessToken = $this->auth->authen($deviceId, $username, $password);
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
                    return response()->json(['error'=>1, 'message'=> __('hanoivip.ddd2::auth.failed'), 'data'=>[]]);
                    else
                        return view('hanoivip::auth.login', ['error' => __('hanoivip.ddd2::auth.failed')]);
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
    
    public function bind(Request $request)
    {
        return $this->doRegister($request);
    }
    
    public function onLoginSuccess(Request $request)
    {
        return view('hanoivip::auth.login-success');
    }
    
    public function onRegisterSuccess(Request $request)
    {
        return view('hanoivip::auth.register-success');
    }
    
}