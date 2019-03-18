<?php

namespace Hanoivip\Ddd2\Services;

use Carbon\Carbon;
use Hanoivip\Ddd2\Models\TabAccount;
use Illuminate\Support\Facades\Cache;

class IdpAuthen implements IDddAuthen
{
    
    public function getUserByToken($token)
    {}

    public function authen($username, $password)
    {}

    
}