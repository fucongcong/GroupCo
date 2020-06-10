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

    public function websocketAction()
    {
        yield $this->render('Web/Views/Default/websocket.html.twig');
    }

    public function sendMsgAction(Request $request)
    {   
        $msg = $request->query->get('msg') ? : '这是一条消息';
        $data = [
            'toFd' => 1,
            'msg' => $msg,
        ];
        $res = (yield \AsyncWebSocket::send('127.0.0.1', 9527, json_encode($data)));

        if ($res) {
            yield "send ok";
        }

        yield "send fail";
    }
}
