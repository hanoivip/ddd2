<?php
namespace Hanoivip\Ddd2\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
//use Illuminate\Database\Eloquent\Model;
use Hanoivip\Ddd2\IDddAuthen;

class AppUser implements AuthenticatableContract, AuthorizableContract
{
    use Authorizable;
    
    public $username;
    
    public $email;
    
    public $id;
    
    public $api_token;
    
    public $expires;
    
    public $channel;
    
    public $createTime;
    
    public function __construct($data = null)
    {
        if (!empty($data))
        {
            $this->id = $data['id'];
            $this->email = $data['email'];
            $this->username = $data['user_name'];
            $this->api_token = $data['api_token'];
            $this->expires = $data['expires'];
            $this->channel = $data['channel'];
            $this->createTime = $data['create_time'];
        }
        else {
            $this->username = "";
            $this->email = "";
            $this->id = 0;
            $this->api_token = "";
            $this->expires = 0;
            $this->channel = 0;
            $this->createTime = 0;
        }
    }
    
    public function fetchUserByCredentials(Array $credentials)
    {   
        $token = $credentials['access_token'];
        $auth = app()->make(IDddAuthen::class);
        $arr_user = $auth->getUserByToken($token);
        if (!empty($arr_user)) {
            $this->username = $arr_user['user_name'];
            $this->email = $arr_user['email'];
            $this->id = $arr_user['id'];
            $this->api_token = $arr_user['api_token'];
            $this->channel = $arr_user['channel'];
            $this->createTime = $arr_user['create_time'];
            return $this;
        }
    }
    
    public function getAuthIdentifierName()
    {
        return $this->username;
    }
    
    public function getAuthIdentifier()
    {
        return $this->id;
    }
    
    public function getAuthPassword()
    {
        //return "";
    }
    
    public function getRememberToken()
    {
        //if (! empty($this->getRememberTokenName())) {
        //    return $this->{$this->getRememberTokenName()};
        //}
    }
    
    public function setRememberToken($value)
    {
        //if (! empty($this->getRememberTokenName())) {
        //    $this->{$this->getRememberTokenName()} = $value;
        //}
    }
    
    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberTokenName()
     */
    public function getRememberTokenName()
    {
        return "";
    }
    
}