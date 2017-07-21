<?php

namespace Group\Channel;

use swoole_process;
use Group\Process;
use Group\Config\Config;
use Redis;
use swoole_redis;

class SubscribeServiceProcess extends Process
{
    public function register()
    {   
        $server = $this->server;
        $process = new swoole_process(function($process) use ($server) {var_dump(1);
            $redis = $this->getRedis();
            $redis->subscribe(array('chan-1'), function($redis, $chan, $msg){
                var_dump($msg);
            });
        });

        return $process;
    }

    private function getRedis()
    {
        if (Config::get("database::cache") != 'redis') return;

        $redis = new Redis;
        $config = Config::get("database::redis");
        $redis->connect($config['default']['host'], $config['default']['port']);

        if (isset($config['default']['auth'])) {
            $redis->auth($config['default']['auth']);
        }

        $redis->setOption(Redis::OPT_PREFIX, isset($config['default']['prefix']) ? $config['default']['prefix'] : '');

        return $redis;
    }
}