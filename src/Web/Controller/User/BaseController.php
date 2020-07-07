<?php

namespace src\Web\Controller\User;

use Controller;

class BaseController extends Controller
{
    protected function getUser()
    {
        $userId = $this->container->getContext('userId', 0);

        $service = (yield service_center('User'));

        $req = new \Api\User\Model\GetUserReq;
        $req->setId($userId);
        $res = (yield $service->call("User::getUser", $req));
        //$res = (yield $service->call("User::getUser", ['id' => $userId]));

        $ret = new \Api\User\Model\GetUserRes;
        $ret->mergeFromString($res);
        $user = $ret->getUser();
        if ($user) {
            $this->container->singleton('twig')->addGlobal('app', ['userId' => $user->getId(), 'user' => $user]);
        } else {
            $this->container->setContext('userId', 0);
        }
    }
}