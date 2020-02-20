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
        $value = '00,40,1D,04,0F,13,16,00,11,01,59,58,4F,16,1D,0B,12,1A,02,47,57,40,0E,0D,1D,0E,0F,0A,0F,47,41,53,5D,5C,4A,4C,43,19,06,17,08,0B,02,0B,5E,5A,43,5E,4D,54,55,53,4F,49,5E,09,05,4D,59,47,4A,03,5B,5D,49,01,55,0A,4E,04,1F,04,59,48,48,51,05,56,4E,5D,4E,51,08,48,4A,06,56,5C,5B,55,49,01,59,01,4E,59,43,43,41,10,08,07,1F,0B,1D,0D,04,4D,59,47,08,03,06,16,09,01,43,43,41,0C,08,21,05,04,12,0E,04,03,41,5F,4B,4E,4F,16,15,07,0F,4D,59,47,4C,5B,5C,20,48,22,51,2E,54,57,43,54,5C,27,0B,4F,51,57,5C,22,27,3E,26,29,52,4E,59,57,56,56,51,3F,21,4F,18,9A';
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
