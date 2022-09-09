<?php

namespace Hanoivip\Ddd2;

interface IDddAuthen
{
    public function authen($device, $username, $password);

    public function getUserByToken($token);
    
    public function getUserById($id);
    
    public function createUser($device, $username, $password);
    
    public function changePassword($username, $newPassword);
    
    public function logout($token);
}