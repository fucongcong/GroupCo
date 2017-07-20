<?php

namespace src\Web\Controller\User;

use Controller;
use Group\Common\ValidatorToolkit;
use JsonResponse;
use Request;

class UserController extends BaseController
{   
    public function demoAction(Request $request)
    {   
        yield $this->getUser();
        //渲染模版 模版的启始路径可在config的view.php配置
        yield $this->render('Web/Views/Default/index.html.twig');
    }

    public function registerAction(Request $request)
    {	
        yield $this->getUser();
    	//post请求
    	if ($request->getMethod() == "POST") {
    		$mobile = $request->request->get('mobile');
    		if (!ValidatorToolkit::mobile($mobile)) {
    			yield new JsonResponse([
		                'msg' => '手机格式错误！',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    		$password = $request->request->get('password');
    		if (!ValidatorToolkit::password($password)) {
    			yield new JsonResponse([
		                'msg' => '密码格式错误！',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    		$user = [
    			'mobile' => $mobile,
    			'password' => $password
    		];
            $service = (yield service_center('User'));
    		$res = (yield $service->call("User::addUser", ['user' => $user]));
    		if ($res) {
    			$response = new JsonResponse([
		                'msg' => '注册成功',
		                'data' => '',
		                'code' => 200
		            ]
		        );
		        $user = (yield $service->call("User::getUser", ['id' => $res]));
		        yield $this->setJwt($request, $res, $response);
    		} else {
    			yield new JsonResponse([
		                'msg' => '注册失败',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    	}

    	//get请求
    	if ($request->getMethod() == "GET") {
    		if ($this->isLogin($request)) {
	    		yield $this->redirect('/demo');
	    	}
    		yield $this->render('Web/Views/User/register.html.twig');
    	}
    }

    public function loginAction(Request $request)
    {	
        yield $this->getUser();
    	//post请求
    	if ($request->getMethod() == "POST") {
    		$mobile = $request->request->get('mobile');
    		if (!ValidatorToolkit::mobile($mobile)) {
    			yield new JsonResponse([
		                'msg' => '手机格式错误！',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    		$password = $request->request->get('password');
    		if (!ValidatorToolkit::password($password)) {
    			yield new JsonResponse([
		                'msg' => '密码格式错误！',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    		$user = [
    			'mobile' => $mobile,
    			'password' => $password
    		];
            $service = (yield service_center('User'));
    		$user = (yield $service->call("User::getUserByMobile", ['mobile' => $mobile]));
    		if (isset($user['password']) && $user['password'] == $password) {

    			$response = new JsonResponse([
		                'msg' => '登录成功',
		                'data' => '',
		                'code' => 200
		            ]
		        );

		        yield $this->setJwt($request, $user['id'], $response);

    		} else {
    			yield new JsonResponse([
		                'msg' => '登录失败',
		                'data' => '',
		                'code' => 400
		            ]
		        );
    		}
    	}

    	//get请求
    	if ($request->getMethod() == "GET") {
    		if ($this->isLogin($request)) {
	    		yield $this->redirect('/demo');
	    	}
    		yield $this->render('Web/Views/User/login.html.twig');
    	}
    }

    public function logoutAction(Request $request)
    {   
        $response = $this->redirect('/demo');
        yield $this->clearJwt($request, $response);
    }

    private function isLogin($request)
    {	
        $userId = $this->getContainer()->getContext('userId', 0);
    	return $userId;
    }

    protected function getUserService()
    {
        return service("user");
    }
}

