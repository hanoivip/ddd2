<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Hanoivip\Events\UserLogin;
use Hanoivip\Ddd2\IDddAuthen;

class Ddd2UserProvider implements UserProvider
{
    private $auth;
    
    public function __construct(IDddAuthen $auth)
    {   
        $this->auth = $auth;
    }
    
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $other = $this->retrieveByCredentials($credentials);
        return $user->getAuthIdentifier() == $other->getAuthIdentifier();
    }
    
    public function retrieveByToken($identifier, $token)
    {
        Log::debug("Ddd2UserProvider retrieveByToken:" . $token);
        $user = $this->auth->getUserByToken($token);
        if (!empty($user) && $user->getAuthIdentifier() > 0)
        {
            $uid = $user->getAuthIdentifier();
            if (!empty($uid))
                event(new UserLogin($uid));
            return $user;
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        Log::debug('Ddd2UserProvider...retrieveByCredentials');
        $username = $credentials['username'];
        $password = $credentials['password'];
        $device = $credentials['device'];
        $hash = md5($username . $password . $device);
        if (Cache::has("ddd2_hash_$hash"))
        {
            return Cache::get("ddd2_hash_$hash");
        }
        $token = $this->auth->authen($device, $username, $password);
        $user = $this->auth->getUserByToken($token);
        if (!empty($user) && $user->getAuthIdentifier() > 0)
        {
            $uid = $user->getAuthIdentifier();
            if (!empty($uid))
                event(new UserLogin($uid));
            Cache::put("ddd2_userid_$uid", $user, now()->addMinutes(30));
            Cache::put("ddd2_hash_$hash", $user, now()->addMinutes(30));
            return $user;
        }
    }

    public function retrieveById($identifier)
    {
        Log::debug('Ddd2UserProvider...retrieveById');
        if (Cache::has("ddd2_userid_$identifier"))
        {
            return Cache::get("ddd2_userid_$identifier");
        }
        $auth = app()->make(IDddAuthen::class);
        $user = $auth->getUserById($identifier);
        if (!empty($user) && $user->getAuthIdentifier() > 0)
        {
            $uid = $user->getAuthIdentifier();
            if (!empty($uid))
                event(new UserLogin($uid));
            return $user;
        }
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        Log::debug('TODO: Ddd2UserProvider...updateRememberToken');
    }
    
}