<?php

namespace Group\Routing\Tests;

use Test;
use Group\Routing\RouteService;

class RouteTest extends Test
{   
    protected $route;

    public function __initialize()
    {
        $this->route = new RouteService();

        $routing = array(
            'user_group'=>[
                'pattern' => '/user/{id}/group/{groupId}',
                'controller' => 'Web:Group:Group:test',
                'methods' => 'GET',
            ],
        );

        $this->route->setRouting($routing);
    }

    public function testDeParse()
    {   
        $uri = $this->route->deParse('user_group', ['id' => 1, 'groupId' => 1]);
        $this->assertEquals('/user/1/group/1', $uri);
    }
}
