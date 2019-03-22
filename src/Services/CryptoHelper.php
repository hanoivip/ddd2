<?php

namespace Hanoivip\Ddd2\Services;

class CryptoHelper
{
   public function encrypt($raw, $keystr)
   {
       $key = unpack('C*', $keystr);
       $data = unpack('C*', $raw);
       
       $iRandom = intval(rand() % count($data)) & 0xFF;
       $KRandom = rand() & 0xFF;
       $encrypt = [];
       $keylen = count($key);
       for ($i=1; $i<=count($data); ++$i)
       {
           if ($i<=$iRandom)
           {
               $data[$i] = ($data[$i] ^ $key[$i % $keylen == 0 ? $keylen : $i % $keylen]) & 0xFF;
               $encrypt[$i] = ($data[$i] ^ $KRandom) & 0xFF;
           }
           else 
           {
               if ($i==$iRandom+1)
                   $encrypt[$i]=$KRandom;
               $data[$i] = ($data[$i] ^ $key[$i % $keylen == 0 ? $keylen : $i % $keylen]) & 0xFF;
               $encrypt[$i+1] = ($data[$i] ^ $KRandom) & 0xFF;
           }
       }
       $encrypt[] = $iRandom;
       $chars = array_map("chr", $encrypt);
       return join($chars);
   }
   
   public function decrypt($value, $key)
   {
       $data = $value;
       $data = unpack('C*', $data);
       $keyarr = unpack('C*', $key);
       return $this->innerDecrypt($data, $keyarr);
   }
   
   public function decryptForServlet($value, $key)
   {
       $data = $this->getByteFromHexString($value);
       $keyarr = unpack('C*', $key);
       return $this->innerDecrypt($data, $keyarr);
   }
   
   public function prepareForServlet($encrypt)
   {
       $e = pack('C*', bin2hex($encrypt));
       return join(',', $e);
   }
   
   private function getByteFromHexString($str)
   {
       $data = str_replace(',', '', $str);
       return unpack('C*', hex2bin($data));
   }
   
   private function innerDecrypt($data, $key)
   {
       //print_r($data);
       //print_r($key);
       $datalen = count($data);
       $keylen = count($key);
       $iRandom = $data[$datalen];
       $KRandom = $data[$iRandom+1];
       $decrypt = [];
       for ($i=1; $i<=$datalen - 2; ++$i)
       {
           if ($i<=$iRandom)
           {
               $data[$i] = ($data[$i] ^ $KRandom) & 0xFF;
               $decrypt[$i] = ($data[$i] ^ $key[$i % $keylen == 0 ? $keylen : $i % $keylen]) & 0xFF;
           }
           else
           {
               $data[$i+1] = ($data[$i+1] ^ $KRandom) & 0xFF;
               $decrypt[$i] = ($data[$i+1] ^ $key[$i % $keylen == 0 ? $keylen : $i % $keylen]) & 0xFF;
           }
       }
       //print_r($decrypt);
       $chars = array_map("chr", $decrypt);
       return join($chars);
   }
}