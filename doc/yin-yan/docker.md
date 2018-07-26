### docker启动项目

#### 首先
1. 克隆项目
2. 执行 =&gt; composer install (如果安装很慢，可以使用国内镜像，但是镜像包会有延迟)
3. 新建一个runtime目录，用于存放日志等cache文件

#### 配置config
1.将app.php中的 ip 设为当前内网ip
2.将database.php中所有redis、mysql的host都设为内网ip
3.将service.php中的service与注册中心host设为内网ip

#### 启动docker
- docker-compose build
- docker-compose up -d

#### 启动http server
- docker-compose exec groupco php server.php

#### 访问 http://localhost:9777/

#### 注册登录Demo
- 新建数据库Demo
- 执行 docker-compose exec groupco app/console sql:migrate 
- 启动基础服务 docker-compose exec groupco app/service start
- 配置本地hosts文件加入： 
    127.0.0.1   groupco.com
- 访问配置的servername =&gt; groupco.com/demo 即可

#### http server使用
* 启动http server =&gt; docker-compose exec groupco php server.php
* 热重启http server =&gt; docker-compose exec groupco php server.php -s reload
* 关闭http server =&gt; docker-compose exec groupco php server.php -s stop
* 重启http server =&gt; docker-compose exec groupco php server.php -s restart

#### 服务使用

* 启动某个服务 =&gt; docker-compose exec groupco app/service user
* 热重启某个服务 =&gt; docker-compose exec groupco app/service user reload
* 关闭某个服务 =&gt; docker-compose exec groupco app/service user stop
* 重启某个服务 =&gt; docker-compose exec groupco app/service user restart
* 启动全部服务 =&gt; docker-compose exec groupco app/service start
* 热重启全部服务 =&gt; docker-compose exec groupco app/service reload
* 关闭全部服务 =&gt; docker-compose exec groupco app/service stop
* 重启全部服务 =&gt; docker-compose exec groupco app/service restart
* 
#### 注意事项
- 目前docker镜像中只内置了redis、nginx与php环境。mysql需自行安装

