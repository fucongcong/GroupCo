<?php

namespace Group\Async;

use Config;

class Encipher
{   
    /**
     * @param $data
     * @return string
     */
    public static function encrypt($data)
    {
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $len = strlen($data);
        $pad = $block - ($len % $block);
        $data .= str_repeat(chr($pad), $pad);
        $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, Config::get("async::encipher"), $data, MCRYPT_MODE_ECB);
        $data = base64_encode($data);

        return $data;
    }

    /**
     * @param $str
     * @return string
     */
    public static function decrypt($str)
    {
        $data = base64_decode($str);

        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, Config::get("async::encipher"), $data, MCRYPT_MODE_ECB);

        $len = strlen($data);
        $pad = ord($data[$len - 1]);

        $data = substr($data, 0, strlen($data) - $pad);
        return $data;
    }
}