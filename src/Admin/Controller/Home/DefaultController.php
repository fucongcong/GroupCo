<?php

namespace src\Admin\Controller\Home;

use Controller;

//请继承Controller
class DefaultController extends Controller
{
    //一个action 与route对应
    public function indexAction()
    {   
    	$res = (yield \AsyncMysql::query("SELECT * FROM `nodes`"));
    	$nodes = $res->getResult();
        $node_center = [];
        foreach ($nodes as $key => $node) {
            if ($node['serverName'] == 'node_center') {
                $node_center = $node;
                unset($nodes[$key]);
            }
        }

        //渲染模版 模版的启始路径可在config的view.php配置
        yield $this->render('Admin/Views/Default/index.html.twig',[
        	'nodes' => $nodes,
            'node_center' => $node_center
        	]);
    }
}
