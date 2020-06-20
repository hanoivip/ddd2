<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Hanoivip\Events\UserLogin;
use Hanoivip\Ddd2\Models\AppUser;
use Hanoivip\Ddd2\IDddAuthen;

class TokenToUserProvider implements UserProvider
{
    public function __construct()
    {   
    }
    
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        Log::debug('TokenUserProvider...validateCredentials');
    }
    
    public function retrieveByToken($identifier, $token)
    {
        Log::debug("TokenUserProvider retrieveByToken:" . $token);
        $key = md5($token);
        if (Cache::has($key))
        {
            $user = Cache::get($key);
            Log::debug("TokenUserProvider retrieveByToken found in cache:" . print_r($user, true));
            return $user;
        }
        else 
        {
            $auth = app()->make(IDddAuthen::class);
            $user = $auth->getUserByToken($token);
            Log::debug("TokenUserProvider retrieveByToken:" . print_r($user, true));
            if ($user->getAuthIdentifier() > 0)
            {
                Cache::put($key, $user, now()->addMinutes(60));
                $uid = $user->getAuthIdentifier();
                if (!empty($uid))
                    event(new UserLogin($uid));
                return $user;
            }
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        Log::debug('TokenUserProvider...retrieveByCredentials');
    }

    public function retrieveById($identifier)
    {
        Log::debug('TokenUserProvider...retrieveById');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        Log::debug('TokenUserProvider...updateRememberToken');
    }

    
}