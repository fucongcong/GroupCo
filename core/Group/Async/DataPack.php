<?php

namespace Group\Async;

use Group\Async\Encipher;

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