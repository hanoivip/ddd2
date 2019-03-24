<?php

namespace Hanoivip\Ddd2\Controllers;
use Illuminate\Http\Request;
use Hanoivip\Ddd2\Services\AdminService;

class AdminController extends Controller
{
    private $admin;
    
    public function __construct(AdminService $admin)
    {
        $this->admin = $admin;
    }
    
    public function getUserInfo(Request $request)
    {
        // Username or UserId
        $uid = $request->get('uid');
        $user = $this->admin->getUserInfo($uid);
        $secure = $this->admin->getUserSecureInfo($uid);
        return ['id' => $user['id'], 'personal' => $user, 'secure' => $secure];
    }
    
    public function resetPassword(Request $request)
    {
        // Username or UserId
        $uid = $request->get('uid');
        if ($this->admin->resetDefaultPassword($uid))
            return response('ok');
        else
            return response('nok');
    }
    
    public function genToken(Request $request)
    {
        // Username or UserId
        $uid = $request->get('uid');
        $token = $this->admin->generateToken($uid);
        return response($token);
    }
}