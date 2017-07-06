<?php

namespace src\Web\Controller\Group;

use Controller;
use Request;
use JsonResponse;

class GroupController extends Controller
{
    public function indexAction()
    {
        //$group=$this->getGroupService()->getGroup(1);

        yield $this->render('Web/Views/Group/index.html.twig',array(
            'group' => $group));
    }

    public function addAction()
    {
        yield new \Response('1');
    }

    public function testAction(Request $request, $id)
    {  
        //串行
        // $start = microtime(true);
        //设置2秒超时
        //$this->getUserService()->setTimeout(2);
        // $users = (yield $this->getUserService()->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
        // dump($users);

        // $users = (yield $this->getUserService()->call("User\User::getUser", ['id' => 1]));
        // dump($users);
        // dump(microtime(true) - $start);

        // //并行
        // $start = microtime(true);
        // $callId1 = $this->getUserService()->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
        // $callId2 = $this->getUserService()->addCall("User\User::getUser", ['id' => 1]);
        // $res = (yield $this->getUserService()->multiCall());
        // dump($res[$callId1]);
        // dump($res[$callId2]);
        // dump(microtime(true) - $start);

        // //异步redis
        // yield \AsyncRedis::set('foo', 'bar');
        // dump(yield \AsyncRedis::get('foo'));
        // $user = json_encode(['foo' => 'bar']);
        // yield \AsyncRedis::hSet('user', 1, $user);
        // dump(yield \AsyncRedis::hGet('user', 1));

        // //异步mysql
        // $res = (yield \AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '18768122222', '11111')"));
        
        // if ($res) {
        //     $result = $res->getResult();
        //     $affectedRows = $res->getAffectedRows();
        //     $id = $res->getInsertId();

        //     $res = (yield \AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}"));
        // }

        //异常处理
        // try {
        //     yield $this->testException();
        //     //yield throwException(new \Exception("Error Processing Request", 1));
        // } catch (\Exception $e) {
        //     echo  $e->getMessage();
        // }

        //异步Log
        // yield AsyncLog::info('hello world');

        // yield AsyncLog::debug('test debug', ['foo' => 'bar']);

        // yield AsyncLog::notice('hello world',[], 'group.com');

        // yield AsyncLog::warning('hello world');

        // yield AsyncLog::error('hello world');

        // yield AsyncLog::critical('hello world');

        // yield AsyncLog::alert('hello world');

        // yield AsyncLog::emergency('hello world');

        yield $this->render('Web/Views/Group/index.html.twig', [
            'user' => $users
            ]);
    }

    public function getUserService()
    {
        return service("user");
    }

    public function testException()
    {
        throw new \Exception("Error Processing Request", 1); 
    }
}
