### Route路由

#### 自定义路由 {#1自定义路由}

#### 动态参数绑定 {#2动态参数绑定}

#### restful API 风格 {#3restful-api-风格}

```
<?php
return array(

        'homepage'=>[
            'pattern' => '/',
            'controller' => 'Web:Home:Default:index',
        ],

        'group'=>[
            'pattern' => '/group/{id}',
            'controller' => 'Web:Group:Group:test',
            'methods' => 'GET',
        ],

        'create_group'=>[
            'pattern' => '/group/{id}',
            'controller' => 'Web:Group:Group:index',
            'methods' => 'POST',
        ],

        'user_group'=>[
            'pattern' => '/user/{id}/group/{groupId}',
            'controller' => 'Web:Group:Group:test',
            'methods' => 'GET',
        ],
);
```

#### 多个分组路由配置config/routing.php {#4多个分组路由配置configroutingphp}

#### Route对象使用 {#5route对象使用}

##### Route::deParse\($alias, $parameters = \[\]\);

> 根据别名反解析路由



