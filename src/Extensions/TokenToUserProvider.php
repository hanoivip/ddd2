<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Hanoivip\Events\UserLogin;
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
        //Log::debug("TokenUserProvider retrieveByToken:" . $token);
        $auth = app()->make(IDddAuthen::class);
        $user = $auth->getUserByToken($token);
        //Log::debug("TokenUserProvider retrieveByToken:" . print_r($user, true));
        if ($user->getAuthIdentifier() > 0)
        {
            $uid = $user->getAuthIdentifier();
            if (!empty($uid))
                event(new UserLogin($uid));
            return $user;
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        Log::debug('TODO: TokenUserProvider...retrieveByCredentials');
    }

    public function retrieveById($identifier)
    {
        Log::debug('TODO: TokenUserProvider...retrieveById');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        Log::debug('TODO: TokenUserProvider...updateRememberToken');
    }

    
}