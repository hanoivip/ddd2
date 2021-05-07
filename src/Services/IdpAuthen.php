<?php

namespace Hanoivip\Ddd2\Services;

use Carbon\Carbon;
use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Mervick\CurlHelper;
use Hanoivip\Ddd2\Models\AppUser;

/**
 * Authentication base on IDP
 * Traffic in text plain
 *
 */
class IdpAuthen implements IDddAuthen
{
    public function __construct()
    {
    }
    
    public function getUserByToken($token)
    {
        if (Cache::has($token))
        {
            return Cache::get($token);
        }
        else
        {
            return new AppUser();
        }
    }

    // udid + username + password + version + channel + isChannel + md5key;
    public function authen($username, $password)
    {
        $version = config('ipd.version', '1.0.0');
        $channel = config('ipd.channel', '1000');
        $id = Str::uuid();//device id!!! each device can not have more than 50 acc
        $request = [
            'username' => $username,
            'password' => $password,
            'channel' => $channel,
            'version' => $version,
            'id' => $id,
            'isChannel' => 0,
            'sign' => md5($id . $username . $password . $version . $channel . '0' . config('ipd.secret')),
        ];
        //$encrypt = $this->crypto->encrypt(json_encode($request), config('ipd.crypt'));
        //$data = $this->crypto->prepareForServlet($encrypt);
        // Http POST
        $uri = config('ipd.uri') . '/load?rdata=' . json_encode($request);
        $response = CurlHelper::factory($uri)->exec();
        if (!empty($response['data']) && isset($response['data']['token']))
        {
            $token = $response['data']['token'];
            $userinfo = $response['data']['userInfo'];
            $appUser = new AppUser([
                'id' => $userinfo['id'],
                'email' => $userinfo['email'],
                'user_name' => $userinfo['userName'],
                'api_token' => $token,
                'channel' => 0,
                'create_time' => $userinfo['createTime']['time']
            ]);
            // cache it
            Cache::put($token, $appUser, Carbon::now()->addDays(7));
            return $token;
        }
    }
    
    // udid + username + password + channel + md5key;
    public function createUser($username, $password)
    {
        $channel = config('ipd.channel', '1000');
        $id = Str::uuid();//device id!!! each device can not have more than 50 acc
        $request = [
            'username' => $username,
            'password' => $password,
            'email' => '',
            'channel' => $channel,
            'id' => $id,
            'isChannel' => 0,
            'sign' => md5($id . $username . $password . $channel . config('ipd.secret')),
        ];
        $uri = config('ipd.uri') . '/create?rdata=' . json_encode($request);
        $response = CurlHelper::factory($uri)->exec();
        if (!empty($response['data']) && isset($response['data']['result']))
        {
            if ($response['data']['result'] == 200)
                return true;
        }
        return __('hanoivip::auth.ipd.register.' . $response['data']['result']);
    }
}