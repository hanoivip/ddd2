<?php

namespace Hanoivip\Ddd2\Tests;

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
        //login data
        //$value = 'F7,B7,EA,F3,F8,FC,E4,E1,F7,E6,F6,AE,AF,B8,E1,EA,FC,E5,ED,F5,A3,BC,B7,B6,B0,E8,FF,F7,F6,FA,F7,E0,B7,A0,A3,BB,A3,A4,B4,B6,FB,E8,B7,A0,B0,BE,A5,A0,FB,F0,A1,B4,A1,B7,A7,B2,F5,A3,B5,A0,F1,BF,A4,B7,AB,EA,A4,F7,B5,F2,A4,B8,A2,FB,A0,BD,F2,A1,A0,A3,A5,AE,B9,B8,F7,E6,F6,FF,F4,B6,A8,AE,B7,B6,B0,FE,E4,F3,EA,FA,F3,E1,F0,B8,A8,A9,E4,F7,F3,E7,E7,ED,A4,AA,B0,A7,B5,E5,F1,F3,FC,AE,AF,B8,D3,C8,D3,D4,A8,A7,A5,B4,D4,DE,D3,BF,D6,D7,AE,D5,A3,BD,A3,A3,AB,CA,D2,A0,A9,A1,AB,BB,A1,A8,D6,BC,B5,EB,05';
        $value = '04,44,19,00,0B,17,12,04,15,05,5D,5C,4B,50,4A,57,51,5E,51,43,53,44,0A,09,19,0A,0B,0E,0B,43,45,57,59,55,4A,48,47,02,03,43,45,44,5C,53,4E,07,01,58,5F,55,52,53,50,03,4D,49,51,08,54,50,52,5F,08,52,19,49,03,5D,53,56,1E,54,5F,04,4F,5C,52,5C,45,4D,5D,03,0F,04,00,11,08,47,51,45,43,53,44,1C,12,1D,16,0B,0A,0A,04,5D,5C,4B,12,19,0F,16,1E,06,55,5D,4A,4B,12,11,03,0B,49,5D,43,3C,24,5B,24,3A,57,53,5D,56,20,3B,52,51,52,3E,22,53,5E,50,24,46,5F,50,54,4A,52,53,5C,23,22,39,22,4B,1C,52';
        $decrypt = $helper->decryptForServlet($value, $deckey);
        print_r($decrypt);
        //$this->assertEquals('{"password":"saksua","channel":1042,"version":"1.4.3","id":"a1f94c84-073e-47d2-a631-b06d6b49b7e1","username":"saksua","isChannel":0,"sign":"4A26956538B9F49BC86547FC3CF1FBDE"}', $decrypt);
        $this->assertEquals('{"password":"123456","channel":1042,"id":"526cd384-59b5-4c31-9a3a-f647a26e7877","email":"","username":"saksua4","sign":"CB2EB3661AD483FF657E99952667DCFD"}', $decrypt);
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
