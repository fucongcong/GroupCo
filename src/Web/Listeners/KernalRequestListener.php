<?php

namespace src\Web\Listeners;

use Listener;
use Event;
use \Firebase\JWT\JWT;
use Config;

class KernalRequestListener extends Listener
{
    public function setMethod()
    {
        return 'onKernalRequest';
    }

    public function onKernalRequest(Event $event)
    {
        $request = $event->getRequest();
        $container = $event->getContainer();

        $userId = $this->getUserId($request);
        if ($userId) {
            $container->setContext('userId', $userId);
         //    $service = (yield service_center('User'));
         //    $res = (yield $service->call("User::getUser", ['id' => $userId]));

         //    $ret = new \Api\User\Model\GetUserRes;
         //    $ret->mergeFromString($res);
         //    $user = $ret->getUser();
        	// if ($user) {
         //        $container->singleton('twig')->addGlobal('app', ['userId' => $user->getId(), 'user' => $user]);
         //    } else {
         //        $container->setContext('userId', 0);
         //    }
        } else {
            $container->setContext('userId', 0);
        }
    }

    private function getUserId($request)
    {
    	$jwt = $request->cookies->get('JWT');

        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            return false;
        }

        $data = JWT::decode($jwt, Config::get('jwt::publicKey'), array('RS256'));
        $data = (array) $data;
        $userId = $data['data'];

        return $userId;
    }
}

