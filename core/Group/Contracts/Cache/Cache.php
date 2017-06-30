<?php

namespace Group\Contracts\Cache;

interface Cache
{
    /**
     * 获取cache
     *
     * @param  cacheName,  name::key
     * @return string|array
     */
    public function get($cacheName);

    /**
     * 设置cache
     *
     * @param  cacheName(string)
     * @param  data(array)
     * @param  expireTime(int)
     */
    public function set($cacheName, $data, $param);

}
