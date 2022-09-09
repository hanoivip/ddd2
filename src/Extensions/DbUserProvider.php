<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Hanoivip\Events\UserLogin;
use Hanoivip\Ddd2\IDddAuthen;

class DbUserProvider implements UserProvider
{
    public function __construct()
    {   
    }
    
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        Log::debug('DbUserProvider...validateCredentials');
    }
    
    public function retrieveByToken($identifier, $token)
    {
        Log::debug('DbUserProvider...retrieveByToken');
    }

    public function retrieveByCredentials(array $credentials)
    {
        Log::debug('TODO: DbUserProvider...retrieveByCredentials');
    }

    public function retrieveById($identifier)
    {
        Log::debug('TODO: DbUserProvider...retrieveById');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        Log::debug('TODO: DbUserProvider...updateRememberToken');
    }

    
}