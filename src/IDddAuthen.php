<?php

namespace Hanoivip\Ddd2;

interface IDddAuthen
{
    /**
     * 
     * @param string $username
     * @param string $password
     * @return string|NULL Token string
     */
    public function authen($username, $password);
    /**
     * 
     * @param string $token
     * @return array
     */
    public function getUserByToken($token);
    /**
     * 
     * @param string $username
     * @param string $password
     */
    public function createUser($username, $password);
}