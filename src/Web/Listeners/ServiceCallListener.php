<?php

namespace src\Web\Listeners;

use Listener;
use Event;
use AsyncFile;
use Group\Async\Client\Tcp;

class ServiceCallListener extends Listener
{
    public function setMethod()
    {
        return 'onServiceCall';
    }

    public function onServiceCall(Event $event)
    {
        $data = $event->getProperty();

        //上报监控平台
        $cmd = $data['cmd'];
        // if (strpos($cmd, "\\") !== false) {
        //     list($cmd, $action) = explode("::", $cmd);
        //     list($group, $service) = explode("\\", $cmd);

        //     $info = [
        //         'server' => $group,
        //         'service' => $service,
        //         'action'  => $action,
        //         'error' => $data['error'],
        //         'calltime' => $data['calltime'],
        //         'ip' => $data['ip'],
        //         'port' => $data['port'],
        //     ];
        //     //直接这么上报肯定是有性能问题的，应该写入日志或者丢入内存，再上报。
        //     //$date = date("YmdHi");
        //     //yield AsyncFile::write(__ROOT__."runtime/monitor/{$date}.log", json_encode($info)."\n", FILE_APPEND);
        //     $monitor = (yield service_center('Monitor'));
        //     $res = (yield $monitor->call('Monitor::add', ['info' => $info], false, false));
        // }
    }
}

