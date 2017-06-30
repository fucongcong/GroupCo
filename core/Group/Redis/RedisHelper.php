<?php

namespace Group\Redis;

class RedisHelper
{
    /**
     * 生成hash所需的参数
     *
     * @param prefix
     * @param id
     * @return list(string,string)
     */
    public static function hashKey($prefix, $id)
    {
        if (is_numeric($id)) {
            $shang = floor($id / 100);
            return [$prefix.':'.$shang, $id % 100];
        } else {
            if (strlen($id) > 2) {
                $shang = substr($id, 0,-2);
                return [$prefix.':'.$shang, substr($id, -2)];
            } else {
                return [$prefix.':', $id];
            }
        }
    }
}
