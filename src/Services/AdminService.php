<?php

namespace Hanoivip\Ddd2\Services;

use Hanoivip\Ddd2\Models\TabAccount;

class AdminService
{
    const DEFAULT_PASSWORD = "toiyeugunpowpri";
    
    private $authenticator;
    
    public function __construct(DddAuthen $auth)
    {
        $this->authenticator = $auth;
    }
    
    private function getUserByIdOrUsername($uid)
    {
        $account = TabAccount::where('id', $uid)
        ->get();
        if ($account->isNotEmpty())
        {
            return $account->first();
        }
        // retry with username
        $account = TabAccount::where('user_name', $uid)
        ->get();
        if ($account->isNotEmpty())
        {
            return $account->first();
        }
    }
    
    public function getUserInfo($uid)
    {
       $user = $this->getUserByIdOrUsername($uid);
       return ['id' => $user->id, 'hoten' => $user->udid];
    }
    
    public function getUserSecureInfo($uid)
    {
        $user = $this->getUserByIdOrUsername($uid);
        return ['email' => $user->email, 'email_verified' => false];
    }
    
    public function resetDefaultPassword($uid)
    {
        $user = $this->getUserByIdOrUsername($uid);
        if (!empty($user))
        {
            $user->password = self::DEFAULT_PASSWORD;
            $user->save();
            return true;
        }
        return false;
    }
    
    public function generateToken($uid)
    {
        $user = $this->getUserByIdOrUsername($uid);
        if (!empty($user))
        {
            $token = $this->authenticator->authen($user->user_name, $user->password);
            return $token;
        }
    }
}