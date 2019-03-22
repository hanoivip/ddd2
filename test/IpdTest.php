<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Hanoivip\Ddd2\Services\CryptoHelper;

class IpdTest extends TestCase
{
    
    public function testDecrypt()
    {
        $helper = new CryptoHelper();
        $deckey = 'pifnwkjdhn';
        $value = '67,27,7A,63,68,74,71,67,76,66,3E,3F,28,30,29,33,31,38,34,34,29,32,3D,32,2F,37,3E,30,34,30,26,35,3A,38,2B,37,3C,38,34,38,2C,35,30,32,2B,35,34,3C,33,32,2C,33,3F,20,37,25,65,60,65,6C,72,60,66,20,21,36,36,3C,3C,2E,3E,73,6F,70,68,6E,69,66,26,38,3E,34,24,36,35,34,24,24,26,6B,78,27,30,20,29,35,32,3F,34,32,2A,30,3D,35,2B,33,36,30,3C,32,2E,3F,3A,32,21,37,36,32,34,32,26,35,3A,38,2B,37,34,3A,30,35,2C,35,3C,37,39,2B,24,7D,77,67,6E,6B,6B,6F,7E,25,3C,2A,36,30,28,32,6C,3A,32,2D,32,31,3F,34,36,2C,3D,32,32,29,3D,36,38,3E,32,2C,3F,3A,32,21,37,36,32,34,32,2E,37,3E,35,2B,37,30,3D,26,2E,3E,6C,79,41,73,66,68,66,61,6E,3E,3F,3A,2E,39,74,6F,6F,6A,20,26,27,38,40,58,3E,33,3E,41,36,2B,32,33,33,58,41,31,3E,42,33,25,30,3B,30,2B,45,3F,3B,31,33,59,36,3B,32,39,7A,98';
        $decrypt = $helper->decryptForServlet($value, $deckey);
        print_r($decrypt);
        $this->assertEquals('{"password":"saksua","channel":1042,"version":"1.4.3","id":"a1f94c84-073e-47d2-a631-b06d6b49b7e1","username":"saksua","isChannel":0,"sign":"4A26956538B9F49BC86547FC3CF1FBDE"}', $decrypt);
    }
    
    public function testEncrypt()
    {
        $helper = new CryptoHelper();
        $deckey = 'pifnwkjdhn';
        $value = '{"password":"saksua","channel":1042,"version":"1.4.3","id":"a1f94c84-073e-47d2-a631-b06d6b49b7e1","username":"saksua","isChannel":0,"sign":"4A26956538B9F49BC86547FC3CF1FBDE"}';
        $encrypt = $helper->encrypt($value, $deckey);
        print_r($encrypt);
        $decrypt = $helper->decrypt($encrypt, $deckey);
        $this->assertEquals($decrypt, $value);
    }
    
    public function testLogin()
    {
    }
}
