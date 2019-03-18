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
        $value = '02,42,1F,06,0D,11,14,02,13,03,5B,5A,4D,14,1F,09,10,18,00,45,55,42,0C,0F,1F,0C,0D,08,0D,45,43,51,5F,53,4C,4E,41,1B,04,15,0A,09,00,09,5C,58,41,5C,4F,53,57,53,4D,4B,5C,0B,07,4F,5B,09,45,18,51,09,5E,4A,01,5B,59,4C,57,4E,53,0A,4A,4A,55,07,5F,4C,06,4F,53,5E,4A,1C,52,55,09,57,05,4D,59,0D,50,1B,53,41,41,43,12,0A,05,1D,09,1F,0F,06,4F,5B,45,0A,01,04,14,0B,03,41,41,43,0E,0A,23,07,06,10,0C,06,01,43,5D,49,4C,4D,14,17,05,0D,4F,5B,45,4D,21,5D,51,47,57,55,58,52,5F,3B,59,29,53,47,20,20,55,57,52,4D,57,29,24,4D,21,25,5C,27,25,3D,25,4D,1A,3B';
        $decrypt = $helper->decryptForServlet($value, $deckey);
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
}
