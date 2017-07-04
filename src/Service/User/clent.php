<?php

function asyncJob($data, $getRecv = false){
    static $client = null;
    if (is_null($client)){
        $client = pfsockopen('0.0.0.0', 9519);
    }
    if (!$client){
        //能否fallback到同步的模式?
        return false;
    }
    fwrite($client, $data . "\r\n");
    if ($getRecv){
        $content = '';
        // stream_set_blocking($client, FALSE );
        //设置一个5s的超时
        stream_set_timeout($client, 3);
        $info = stream_get_meta_data($client);
        while (!$info['timed_out']) {
            $content .= fread($client, 8192);
            if (stristr($content,"\r\n")){
                break;
            }
            $info = stream_get_meta_data($client);
        }
        //不一定一定是json对象
        return trim($content);
    }
}

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
        $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, 'uoI49l^^M!a5&bZt', $data, MCRYPT_MODE_ECB);
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

        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, 'uoI49l^^M!a5&bZt', $data, MCRYPT_MODE_ECB);

        $len = strlen($data);
        $pad = ord($data[$len - 1]);

        $data = substr($data, 0, strlen($data) - $pad);
        return $data;
    }
}
class DataPack 
{
    public static function pack($cmd = '', $data = [])
    {   
        if (is_array($cmd)) {
            $token = Encipher::encrypt(implode(",", $cmd));
        } else {
            $token = Encipher::encrypt($cmd);
        }
        
        return json_encode(['cmd' => $cmd, 'data' => $data, 'token' => $token]);
    }

    public static function unpack($data = [])
    {
        $data = json_decode($data, true);

        if (is_array($data['cmd'])) {
            $token = implode(",", $data['cmd']);
        } else {
            $token = $data['cmd'];
        }

        if (!isset($data['token']) || Encipher::decrypt($data['token']) != $token) throw new \Exception("Error Auth!", 1);
        
        return [$data['cmd'], $data['data']];
    }
}
$startTime = microtime(true);
$cmd = "User\User::getUser";
$data = ['id' => 1];
//$data = [1,2,3,4,5,6,7,8,9,10,1,2,3,4,5,6,7,8,9,10];
$data = DataPack::pack($cmd, $data);
var_dump(asyncJob($data, true));


$endTime = microtime(true);
var_dump($endTime - $startTime);