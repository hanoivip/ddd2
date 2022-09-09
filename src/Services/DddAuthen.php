<?php

namespace Hanoivip\Ddd2\Services;

use Carbon\Carbon;
use Hanoivip\Ddd2\IDddAuthen;
use Hanoivip\Ddd2\Models\TabAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Ddd2\Models\AppUser;

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
    public function authen($device, $username, $password)
    {
        $account = TabAccount::where('user_name', $username)
        ->where('password', $password)
        ->get();
        if ($account->isNotEmpty())
        {
            $account = $account->first();
            $token = uniqid();
            //$account["api_token"] = $token;
            $account = new AppUser([
                'id' => $account['id'],
                'email' => $account['email'],
                'user_name' => $account['user_name'],
                'api_token' => $token,
                'expires' => Carbon::now()->addDays(30),
                'channel' => 0,
                'create_time' => 0, //$userinfo['createTime']['time']
            ]);
            Log::debug('Generated token:' . $token);
            Cache::put($token, $account, $account->expires);
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
        //Log::debug('Get user by token:' . $token);
        if (Cache::has($token))
        {
            return Cache::get($token);
        }
        else
        {
            return new AppUser();
        }
    }
    
    public function getUserById($id)
    {
        throw new Exception("TODO: implement getUserById");
    }
    
    public function createUser($device, $username, $password)
    {
        throw new Exception("TODO: implement createUser");
    }
    
    public function changePassword($username, $newPassword)
    {
        throw new Exception("TODO: implement changePassword");
    }
    
    public function logout($token)
    {
        if (Cache::has($token))
            Cache::forget($token);
    }
}