### 启动项目\(请先完成环境依赖安装\)

1. 克隆项目
2. 执行 =&gt; composer install
3. 新建一个runtime目录，用于存放日志等cache文件
4. 配置config中的database配置文件
5. 设置config/service.php中的registry\_address.目前只支持redis、mysql作为注册中心
6. 启动http server =&gt; php server.php
7. 访问 [http://localhost:9777/](http://localhost:9777/) 开始异步协程之旅

#### Demo(将用nginx做一次反向代理资源文件)

* 修改配置nginx，见doc/nginx.md,配置hosts
* 配置config中的service配置文件
* 执行脚本 =&gt; app/console sql:migrate 
* 启动user服务 =&gt; app/service user
* 使用监控Monitor服务 app/service monitor
* 还可以启动其他服务，自行配置
* 访问配置的servername =&gt; groupco.com/demo 即可

#### 更新代码

* 执行 =&gt; composer update

#### 使用

* 启动http server =&gt; php server.php
* 热重启htt pserver =&gt; php server.php -s reload
* 关闭http server =&gt; php server.php -s stop
* 重启http server =&gt; php server.php -s restart
* 启动某个服务 =&gt; app/service user
* 热重启某个服务 =&gt; app/service user reload
* 关闭某个服务 =&gt; app/service user stop

#### 特别注意

* 1.额外内存释放的问题，局部静态变量，全局变量的释放。
* 2.断线重连机制内部已封装\(在执行sql时如果出现长连接已失效，将尝试3次重连操作\)。