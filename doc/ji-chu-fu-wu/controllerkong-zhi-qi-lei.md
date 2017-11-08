### Controller控制器

#### 第一个控制器 {#1第一个控制器}

```
<?php

namespace src\Web\Controller\Group;

use Controller;
use Request;
use JsonResponse;

class GroupController extends Controller
{
    public function indexAction()
    {
        yield $this->render('Web/Views/Group/index.html.twig',array(
            'group' => $group));
    }

    public function addAction()
    {
        yield new \Response('1');
    }
}
```

#### 如何获取路由传过来的参数？详见Request与Route服务 {#2如何获取路由传过来的参数？详见request与route服务}

#### 需返回自定义Response，详见Response服务 {#3需返回自定义response，详见response服务}

#### Controller对象中的方法 {#4controller对象中的方法}

* ###### public function render\($tpl, $array = array\(\)\) {#public-function-rendertpl-array--array}
* ###### public function createService\($serviceName\) {#public-function-createserviceservicename}
* ###### public function redirect\($url, $status = 302\) {#public-function-redirecturl-status--302}
* ###### public function route\(\) {#public-function-route}
* ###### public function twigInit\(\) {#public-function-twiginit}
* ###### public function getContainer\(\) {#public-function-twiginit}
* ###### public function setJwt\($request, $data, $response\) {#public-function-twiginit}
* ###### public function pasreJwt\($request\) {#public-function-twiginit}
* ###### public function clearJwt\($request, $response\) {#public-function-twiginit}



