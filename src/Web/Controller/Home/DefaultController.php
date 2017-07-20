<?php

namespace src\Web\Controller\Home;

use Controller;
use Request;

//请继承Controller
class DefaultController extends Controller
{
    //一个action 与route对应
    public function indexAction(Request $request)
    {   
        $users = (yield $this->getUserService()->call("User\User::getUser", ['id' => 1]));
        yield "hello world!";
    }

    public function demoAction(Request $request)
    {   
        //渲染模版 模版的启始路径可在config的view.php配置
        yield $this->render('Web/Views/Default/index.html.twig');
    }

    public function getUserService()
    {
        return service("user");
    }
}
