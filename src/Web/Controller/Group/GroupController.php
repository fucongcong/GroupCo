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

        yield $this->render('Web/Views/Group/index.html.twig', [
            'user' => $users
            ]);
    }

    public function getUserService()
    {
        return service("user_service");
    }

}
