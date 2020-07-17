<?php 

define('ASYNC', false);
define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "../");

use Group\Sync\SyncApp;
use Group\Protocol\ServiceReqProtocol;
use Group\Protocol\ServiceResProtocol;
use Group\Protocol\Client;
require __DIR__.'/../vendor/autoload.php';
$app = new SyncApp();
$app->initSelf();
$app->registerServices();
$app->singleton('container')->setAppPath(__ROOT__);

//调用命令
$cmd = "User/User/getUser";
//封装请求
$req = new Api\User\Model\GetUserReq;
$req->setId(1);
$data = ServiceReqProtocol::pack($cmd, $req);

//建立一个客户端
$client = new Client('0.0.0.0', 9511, true);
$cli = $client->getClient();
$res = $cli->call($data);
//获得响应
if ($res['response']) {
    $res = ServiceResProtocol::unpack($res['response']);
    dump($res->serializeToJsonString());
    if ($res->getCode() == 200) {
      try {
        $ret = new Api\User\Model\GetUserRes;
        $ret->mergeFromString(ServiceResProtocol::getData($res));
        dump($ret->getUser()->getId());
        dump($ret->getUser()->getMobile());
      } catch (Exception $e) {}
    } else {
      dump($res->getErrMsg());
    }
}
