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
        Log::debug('IpdUserProvider...validateCredentials');
        //Log::debug(print_r($user, true));
        //Log::debug(print_r($credentials, true));
        $other = $this->retrieveByCredentials($credentials);
        return $user->getAuthIdentifier() == $other->getAuthIdentifier();
    }
    
    public function retrieveByToken($identifier, $token)
    {
        //Log::debug("TokenUserProvider retrieveByToken:" . $token);
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
        Log::debug('IpdUserProvider...retrieveByCredentials');
        $username = $credentials['username'];
        $password = $credentials['password'];
        $device = $credentials['device'];
        $token = $this->auth->authen($device, $username, $password);
        $user = $this->auth->getUserByToken($token);
        if (!empty($user) && $user->getAuthIdentifier() > 0)
        {
            $uid = $user->getAuthIdentifier();
            if (!empty($uid))
                event(new UserLogin($uid));
            //Log::debug(print_r($user, true));
            return $user;
        }
    }

    public function retrieveById($identifier)
    {
        //Log::debug('IpdUserProvider...retrieveById');
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
        Log::debug('TODO: IpdUserProvider...updateRememberToken');
    }
    
}