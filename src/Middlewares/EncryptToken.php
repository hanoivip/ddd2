<?php

namespace Hanoivip\Ddd2\Middlewares;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;


class EncryptToken
{
    private $inputKey = 'access_token';
    
    protected $encrypter;
    
    public function __construct(EncrypterContract $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function handle($request, Closure $next)
    {
        return $this->encrypt($next($this->decrypt($request)));
    }
    
    protected function decrypt(Request $request)
    {
        try
        {
            $token = $request->query($this->inputKey);
            
            if (empty($token)) {
                $token = $request->input($this->inputKey);
                if (!empty($token))
                {
                    Log::debug('EncryptToken token ' . $token);
                    $decrypted = $this->encrypter->decrypt($token);
                    $request->replace($this->inputKey, $decrypted);
                }
            }
            
            if (empty($token)) {
                $token = $request->bearerToken();
                if (!empty($token))
                {
                    Log::debug('EncryptToken token ' . $token);
                    $decrypted = $this->encrypter->decrypt($token);
                    $request->headers->set('Authorization', 'Bearer: ' . $decrypted);
                }
            }
        }
        catch (DecryptException $ex)
        {
            Log::error('Token is not recorgnized. Msg:' . $ex->getMessage());
        }
        
        return $request;
    }
    
    
    protected function encrypt(Response $response)
    {
        return $response;
    }
    
    
}
