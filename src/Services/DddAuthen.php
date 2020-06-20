<?php

namespace Hanoivip\Ddd2\Services;

use Carbon\Carbon;
use Hanoivip\Ddd2\IDddAuthen;
use Hanoivip\Ddd2\Models\TabAccount;
use Illuminate\Support\Facades\Cache;
use Exception;

class DddAuthen implements IDddAuthen
{   
    /**
     * 1. Query user in database
     * 2. Generate and cache tokens
     * 3. Return token
     * 
     * @param string $username
     * @param string $password
     * @return string
     */
    public function authen($username, $password)
    {
        $account = TabAccount::where('user_name', $username)
        ->where('password', $password)
        ->get();
        if ($account->isNotEmpty())
        {
            $account = $account->first();
            $token = uniqid();
            $account["api_token"] = $token;
            Cache::put($token, $account, Carbon::now()->addDays(7));
            return $token;
        }
    }
    
    /**
     * 1. Check for token in cache
     * 2. Return, if any
     * 
     * @param string $token
     * @return array
     */
    public function getUserByToken($token)
    {
        if (Cache::has($token))
        {
            return Cache::get($token);
        }
    }
    
    public function createUser($username, $password)
    {
        throw new Exception("Not support method");
    }

    
    
}