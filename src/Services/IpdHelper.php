<?php

namespace Hanoivip\Ddd2\Services;

class IdpHelper
{
    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;    
    }
    
    public function getAccountInfo($token)
    {
    }
}