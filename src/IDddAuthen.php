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
    public function authen($device, $username, $password);
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
    public function createUser($device, $username, $password);
    
    public function changePassword($username, $newPassword);
    
    public function guest($device);
    
    public function bind($device, $username, $password);
}