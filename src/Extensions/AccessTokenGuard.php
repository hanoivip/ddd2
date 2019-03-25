<?php 

namespace Hanoivip\Ddd2\Extensions;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessTokenGuard implements Guard
{
    use GuardHelpers;
    
    private $inputKey = '';
    private $request;
    
    public function __construct (UserProvider $provider, Request $request, $configuration) {
        $this->provider = $provider;
        $this->request = $request;
        $this->inputKey = isset($configuration['input_key']) ? $configuration['input_key'] : 'ddd2_token';
    }
    
    public function user()
    {
        if (!empty($this->user)) 
        {
            return $this->user;
        }
        
        $user = null;
        
        // retrieve via token
        $token = $this->getTokenForRequest();
        Log::debug("Guard: token passed:" . $token);
        
        if (!empty($token)) {
            // the token was found, how you want to pass?
            $user = $this->provider->retrieveByToken($this->inputKey, $token);
        }
        
        return $this->user = $user;
    }
    
    /**
     * Get the token for the current request.
     * @return string
     */
    public function getTokenForRequest () {
        $token = $this->request->query($this->inputKey);
        //Log::debug('... xxx ' . print_r($this->request, true));
        
        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }
        
        if (empty($token)) {
            $token = $this->request->bearerToken();
        }
        
        if (empty($token))
        {
            //Log::debug('...' . print_r($this->request->cookies, true));
            $token = $this->request->cookies->get($this->inputKey);
        }
        
        return $token;
    }

    public function validate(array $credentials = [])
    {
    }

}