<?php 

define('ASYNC', false);
define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "../");

use Group\Sync\SyncApp;
require __DIR__.'/../vendor/autoload.php';

$app = new SyncApp();
$app -> initSelf();
$app -> registerServices();
$app -> singleton('container') -> setAppPath(__ROOT__);
use Group\Protocol\ServiceReqProtocol;
use Group\Protocol\ServiceResProtocol;

//测试调用 User模块的UserService下的getUser方法
//see Api/User/UserService
$cmd = "User\User::getUser";

//使用pb调用
$req = new Api\User\Model\GetUserReq;
$req->setId(1);
$req->serializeToString();

$data = ServiceReqProtocol::pack($cmd, $req);
$res = asyncJob($data, true);
$res = ServiceResProtocol::unpack($res);

try {
  $ret = new Api\User\Model\GetUserRes;
  $ret->mergeFromString($res->getData());
  dump($ret->getUser()->getId());
  dump($ret->getUser()->getMobile());

  $json = $ret->serializeToJsonString();
  dump(json_decode($json, true));
} catch (Exception $e) {
  
}

echo PHP_EOL;
//使用php的数组形式调用
$data = ['id' => 1];

$data = ServiceReqProtocol::pack($cmd, $data);
$res = asyncJob($data, true);
$res = ServiceResProtocol::unpack($res);

try {
  $ret = new Api\User\Model\GetUserRes;
  $ret->mergeFromString($res->getData());
  dump($ret->getUser()->getId());
  dump($ret->getUser()->getMobile());

  $json = $ret->serializeToJsonString();
  dump(json_decode($json, true));
} catch (Exception $e) {
  
}


function asyncJob($data, $getRecv = false){
    static $client = null;
    if (is_null($client)){
        $client = pfsockopen('0.0.0.0', 9511);
    }
    if (!$client){
        //能否fallback到同步的模式?
        return false;
    }
    fwrite($client, $data);
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