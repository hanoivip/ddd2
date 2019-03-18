<?php

namespace Hanoivip\Ddd2\Services;

interface IDddAuthen
{

    public function authen($username, $password);
    
    public function getUserByToken($token);
}