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
        yield "hello world!";
    }
}
