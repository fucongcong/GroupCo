<?php

namespace src\Admin\Controller\Index;

use Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        yield $this->render('Admin/Views/Index/index.html.twig');
    }

}

