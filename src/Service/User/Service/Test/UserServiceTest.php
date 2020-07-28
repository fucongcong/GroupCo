<?php 

namespace src\Service\User\Service\Test;

use Test;

class UserServiceTest extends Test
{   
    public function unitGetUser()
    {   
        $req = new \Api\User\Model\GetUserReq;
        $req->setId(1);

        $res = (yield service('test')->call("User\User::getUser", $req));

        $ret = new \Api\User\Model\GetUserRes;
        $ret->mergeFromString($res);
        $user = $ret->getUser();

        $this->assertNotNull($user);
        if ($user) {
            $this->assertEquals(1, $user->getId());
        }
    }
}