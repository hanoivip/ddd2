<?php

namespace Hanoivip\Ddd2\Services;

use Hanoivip\Ddd2\IDddAuthen;
use Illuminate\Support\Facades\Cache;


class IdpAuthen implements IDddAuthen
{
    private $crypto;
    
    public function __construct(CryptoHelper $crypto)
    {
        $this->crypto = $crypto;
    }
    
    public function getUserByToken($token)
    {
        if (Cache::has($token))
        {
            return Cache::get($token);
        }
        // Retrive from Ipd
    }

    public function authen($username, $password)
    {
        $version = config('ipd.version', '1.0.0');
        $channel = config('ipd.channel', '1000');
        $id = str_random();//device id!!! each device can not have more than 50 acc
        $request = [
            'username' => $username,
            'password' => $password,
            'channel' => $channel,
            'version' => $version,
            'id' => $id,
            'isChannel' => 0,
            'sign' => md5($id . $username . $password . $channel . '0' . config('ipd.secret')),
        ];
        $encrypt = $this->crypto->encrypt(json_encode($request), config('ipd.crypto'));
        $data = $this->crypto->prepareForServlet($encrypt);
        // Http POST
        $uri = config('ipd.uri') . '/load?data=' . $data;
        $response = \CurlHelper::factory($uri)->exec();
        if (!empty($response['data']) && isset($response['data']['token']))
        {
            $token = $response['data']['token'];
            return $token;
        }
    }

    
}