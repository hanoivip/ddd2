<?php

namespace Hanoivip\Ddd2\Services;

use Carbon\Carbon;
use Hanoivip\Ddd2\IDddAuthen;
use Hanoivip\Ddd2\Models\AppUser;
use Hanoivip\Events\User\PassUpdated;
use Hanoivip\User\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mervick\CurlHelper;
use Exception;

/**
 * Authentication base on IDP
 * Traffic in text plain
 *
 * 202109: cache user in database
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
    public function authen($device, $username, $password)
    {
        $version = config('ipd.version', '1.0.0');
        $channel = config('ipd.channel', '1000');
        $device = substr($device, 0,50);
        $request = [
            'username' => $username,
            'password' => $password,
            'channel' => $channel,
            'version' => $version,
            'id' => $device,
            'isChannel' => 0,
            'sign' => md5($device . $username . $password . $version . $channel . '0' . config('ipd.secret')),
        ];
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
                'expires' => Carbon::now()->addDays(30),
                'channel' => 0,
                'create_time' => $userinfo['createTime']['time'],
                'device' => $userinfo['udid'],
            ]);
            // cache it
            Cache::put($token, $appUser, $appUser->expires);
            // cache user in db
            $record = User::find($userinfo['id']);
            if (!$record)
            {
                $record = new User();
                $record->id = $userinfo['id'];
                // IPD case sensitive but laravel users base incase sensitive
                $record->name = md5($username);
                $record->password = Hash::make($password);
                $record->save();
            }
            else
            {
                $record->password = Hash::make($password);
                $record->save();
            }
            return $token;
        }
    }
    
    // udid + username + password + channel + md5key;
    public function createUser($device, $username, $password)
    {
        $channel = config('ipd.channel', '1000');
        $device = substr($device,0,50);
        $request = [
            'username' => $username,
            'password' => $password,
            'email' => '',
            'channel' => $channel,
            'id' => $device,
            'isChannel' => 0,
            'sign' => md5($device . $username . $password . $channel . config('ipd.secret')),
        ];
        $uri = config('ipd.uri') . '/create?rdata=' . json_encode($request);
        $response = CurlHelper::factory($uri)->exec();
        if (!empty($response['data']) && isset($response['data']['result']))
        {
            if ($response['data']['result'] == 200)
            {
                return true;
            }
        }
        return __('hanoivip.ddd2::auth.ipd.register.' . $response['data']['result']);
    }

    public function changePassword($uid, $newPassword)
    {
        $request = [
            'uid' => $uid,
            'password' => "",
            'newpassword' => $newPassword,
            'sign' => md5($uid . $newPassword . config('ipd.secret')),
        ];
        $uri = config('ipd.uri') . '/modifyPassword?rdata=' . json_encode($request);
        $response = CurlHelper::factory($uri)->exec();
        Log::debug($response['content']);
        if (!empty($response['data']) && isset($response['data']['result']))
        {
            if ($response['data']['result'] == 200)
            {
                return true;
            }
        }
        return __('hanoivip.ddd2::auth.ipd.register.' . $response['data']['result']);
    }

    public function handle(PassUpdated $event)
    {
		if ($event instanceof PassUpdated)
		{
			Log::debug("We are gonna update accoutn password..." . print_r($event, true));
			return $this->changePassword($event->uid, $event->newpass);
		}
    }
    
    public function getUserById($id)
    {
        $version = config('ipd.version', '1.0.0');
        $channel = config('ipd.channel', '1000');
        $request = [
            'channel' => $channel,
            'version' => $version,
            'id' => $id,
            'sign' => md5($id . $version . $channel . '1' . config('ipd.secret')),
        ];
        // Http POST
        $uri = config('ipd.uri') . '/load?idata=' . json_encode($request);
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
                'expires' => Carbon::now()->addDays(30),
                'channel' => 0,
                'create_time' => $userinfo['createTime']['time'],
                'device' => $userinfo['udid'],
            ]);
            return $appUser;
        }
    }

    public function logout($token)
    {
        if (Cache::has($token))
            Cache::forget($token);
    }
}