<?php

namespace src\Admin\Controller\Index;

use Controller;
use Request;
use AsyncService;
use AsyncMysql;

class IndexController extends Controller
{
    public function addNodeAction(Request $request)
    {	
    	$ip = $request->request->get('ip');
    	$port = $request->request->get('port');
        $serverName = $request->request->get('serverName');
    	$services = $request->request->get('services');

    	if ($ip =="" || $port == "" || $services == "") {
    		yield 0;
    	}

    	$isExist = (yield AsyncMysql::query("SELECT id FROM `nodes` WHERE ip = '{$ip}' and port = '{$port}' "));

    	if ($isExist->getResult()) {
    		$res = (yield AsyncMysql::query("UPDATE `nodes`  SET `status` = 'active', `services` = '{$services}', `serverName` = '{$serverName}' WHERE ip = '{$ip}' and port = '{$port}'"));
    	} else {
    		$res = (yield AsyncMysql::query("INSERT INTO `nodes` (`ip`, `port`, `status`, `services`, `serverName`) VALUES ('{$ip}', '{$port}', 'active', '{$services}', '{$serverName}')"));
    	}

        if ($res && $res->getResult()) {
        	yield 1;
        }

        yield 0;
    }

    public function removeNodeAction(Request $request)
    {	
    	$ip = $request->request->get('ip');
    	$port = $request->request->get('port');

    	if ($ip =="" || $port == "") {
    		yield 0;
    	}

    	$isExist = (yield AsyncMysql::query("SELECT id FROM `nodes` WHERE ip = '{$ip}' and port = '{$port}' "));

    	if ($isExist && $isExist->getResult()) {
    		$res = (yield AsyncMysql::query("UPDATE `nodes`  SET `status` = 'close',`serviceStatus` = 'offline' WHERE ip = '{$ip}' and port = '{$port}'"));

    		if ($res && $res->getResult()) {
    			yield 1;
    		}
    		yield 0;
    	} else {
    		yield 1;
    	}
    }

    public function closeNodeAction(Request $request)
    {
        $ip = $request->request->get('ip');
        $port = $request->request->get('port');

        $service = new AsyncService($ip, $port);
        $res = (yield $service->call('close'));
        if ($res) {
            yield AsyncMysql::query("UPDATE `nodes`  SET `serviceStatus` = 'offline' WHERE ip = '{$ip}' and port = '{$port}'");
            $res = (yield AsyncMysql::query("UPDATE `nodes`  SET `status` = 'close' WHERE ip = '{$ip}' and port = '{$port}'"));
        }

        yield 1;
    }

    public function reloadNodeAction(Request $request)
    {
        $ip = $request->request->get('ip');
        $port = $request->request->get('port');

        $service = new AsyncService($ip, $port);
        $res = (yield $service->call('reload'));

        yield 1;
    }

    public function deleteNodeAction(Request $request)
    {
        $ip = $request->request->get('ip');
        $port = $request->request->get('port');

        yield AsyncMysql::query("UPDATE `nodes`  SET `serviceStatus` = 'offline' WHERE ip = '{$ip}' and port = '{$port}'");

        $res = (yield AsyncMysql::query("DELETE FROM `nodes` WHERE ip = '{$ip}' and port = '{$port}'"));

        $service = new AsyncService($ip, $port);
        $res = (yield $service->call('close'));

        yield 1;
    }

    public function onlineNodeAction(Request $request)
    {   
        $ip = $request->request->get('ip');
        $port = $request->request->get('port');
        
        $res = (yield AsyncMysql::query("UPDATE `nodes`  SET `serviceStatus` = 'online' WHERE ip = '{$ip}' and port = '{$port}'"));
        yield $this->updateService();
        yield 1;
    }

    public function offlineNodeAction(Request $request)
    {   
        $ip = $request->request->get('ip');
        $port = $request->request->get('port');

        $res = (yield AsyncMysql::query("UPDATE `nodes`  SET `serviceStatus` = 'offline' WHERE ip = '{$ip}' and port = '{$port}'"));
        yield $this->updateService();
        yield 1;
    }

    private function updateService()
    {
        $res = (yield service('node_center')->call("NodeCenter\NodeCenter::updateService", []));
    }
}

