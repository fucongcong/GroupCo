<?php

namespace Group\Async\Tests;

use Test;

class AsyncServiceTest extends Test
{
    public function unitservice()
    {   
        $user = (yield service('user')->call("User\User::getUser", ['id' => 1]));
        $this->assertArrayHasKey('id', $user);
    }

    // public function unitservicecenter()
    // {   
    //     $userService = (yield service_center('User'));
    //     $res = (yield $userService->call('User::getUser', ['id' => 1]));
    //     $this->assertArrayHasKey('id', $user);
    // }

    public function unitexception()
    {
        try {
            throw new \Exception("Error", 1);
        } catch (\Exception $e) {
            $this->assertEquals('Error', $e->getMessage());
        }
    }
}
