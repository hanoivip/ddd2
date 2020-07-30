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
        $value = 'F9,B9,E4,FD,F6,EA,EF,F9,E8,F8,A0,A1,B6,EF,E4,F2,EB,E3,FB,BE,AE,B9,F7,F4,E4,F7,F6,F3,F6,BE,B8,AA,A4,A8,B7,B5,BA,E0,FF,EE,F1,F2,FB,F2,A7,A3,BA,A7,B4,A9,AC,AC,B6,B0,A7,F0,FC,B4,A0,BE,B7,A9,A2,FF,E1,AA,A0,A2,B7,A9,BB,F9,A1,B1,B1,FA,AB,A7,B7,A5,E3,A8,F5,B1,E3,AF,AC,A1,FB,AE,B4,FE,A3,A4,B2,AE,BA,BA,B8,E9,F1,FE,E6,F2,E4,F4,FD,B4,A0,BE,F1,FA,FF,EF,F0,F8,BA,BA,B8,F5,F1,D8,FC,FD,EB,F7,FD,FA,B8,A6,B2,B7,B6,EF,EC,FE,F6,B4,A0,BE,C1,AD,A6,A9,F2,B7,AB,AD,A4,AC,D9,BB,AE,D5,DF,B1,AF,AA,D5,A8,AF,BB,AC,A1,A4,C0,AB,DB,A3,DC,AF,C4,DE,B6,E1,90';
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
