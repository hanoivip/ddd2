<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Hanoivip\User\Facades\DeviceFacade;
use Hanoivip\User\Facades\TwofaFacade;

class DeviceTokenGuard implements Guard
{
    use GuardHelpers;
    
    private $inputKey = '';
    private $request;
    
    public function __construct (UserProvider $provider, Request $request, $configuration) {
        $this->provider = $provider;
        $this->request = $request;
        $this->inputKey = isset($configuration['input_key']) ? $configuration['input_key'] : 'access_token';
    }
    
    public function user()
    {
        if (!empty($this->user)) 
        {
            return $this->user;
        }
        
        $user = null;
        
        $token = $this->getTokenForRequest();
        if (!empty($token)) {
            $record = DeviceFacade::getDeviceByToken($token);
            $deviceInfo = $this->request->get('device');
            if (!empty($record))
            {
                $userId = $record->user_id;
                $user = $this->provider->retrieveById($userId);
                if (TwofaFacade::needVerifyDevice($userId, $deviceInfo))
                {
                    Log::error("DeviceTokenGuard need to verify device!");
                    $user = null;
                }
            }
        }
        
        return $this->user = $user;
    }
    
    /**
     * Get the token for the current request.
     * @return string
     */
    public function getTokenForRequest () {
        $token = $this->request->query($this->inputKey);
        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }
        if (empty($token)) {
            $token = $this->request->bearerToken();
        }
        if (empty($token)) {
            $token = $this->request->cookies->get($this->inputKey);
        }
        if (empty($token) && $this->request->hasHeader($this->inputKey))
        {
            $token = $this->request->header($this->inputKey);
        }
        return $token;
    }

    public function validate(array $credentials = [])
    {
    }
    
    public function logout()
    {
        
    }

}