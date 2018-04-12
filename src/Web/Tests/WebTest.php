<?php

namespace src\Web\Tests\Api;

use Test;
use AsyncHttp;

class AuthTest extends Test
{
    public function unitLoginAction()
    {
        $http = new AsyncHttp('http://127.0.0.1:9777');

        $res = (yield $http->post('/'));
        $this->assertEquals('hello world!', $res->body);
    }
}