<?php
namespace Hanoivip\Ddd2\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Hanoivip\Ddd2\Services\DddAuthen;

class AppUser extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authorizable;
    
    protected $guard_name = 'web';
    
    protected $username;
    
    protected $email;
    
    protected $id;
    
    public $api_token;
    
    protected $fillable = [
        'id'
    ];
    
    public function __construct($data = null)
    {
        if (!empty($data))
        {
            $this->id = $data['id'];
            $this->email = $data['email'];
            $this->username = $data['name'];
            $this->api_token = $data['api_token'];
            $this->attributes['id'] = $data['id'];
        }
    }
    
    public function fetchUserByCredentials(Array $credentials)
    {   
        $token = $credentials['access_token'];
        $auth = new DddAuthen();
        $arr_user = $auth->getUserByToken($token);
        if (!empty($arr_user)) {
            $this->username = $arr_user['user_name'];
            $this->email = $arr_user['email'];
            $this->id = $arr_user['id'];
            $this->api_token = $arr_user['api_token'];
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