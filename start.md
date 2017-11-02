#### 环境依赖
- [hiredis](https://github.com/redis/hiredis)
- redis
- mysql
- php >5.6, php > 7.0
- swoole >=1.9.17 (在编译swoole时加入--enable-async-redis，开启异步redis客户端, --enable-openssl开启openssl支持,--with-openssl-dir指定你的openssl目录)

#### 安装(请先完成环境依赖安装)
- 克隆项目
- 执行 => composer install
- 新建一个runtime目录，用于存放日志等cache文件
- 配置config中的database配置文件
- 设置config/service.php中的registry_address.目前只支持redis作为服务中心
- 启动http server => php server.php
- 访问 http://localhost:9777/ 开始异步协程之旅
- demo 查看，请继续一下步骤，将用nginx做一次反向代理资源文件
- 修改配置nginx，见doc/nginx.md,配置hosts
- 配置config中的service配置文件
- 执行脚本 => app/console sql:migrate 
- 启动user服务 => app/service user
- 使用监控Monitor服务 app/service monitor
- 还可以启动其他服务，自行配置
- 访问配置的servername => groupco.com/demo 即可

#### 更新代码
- 执行 => composer update

#### 使用
- 启动http server => php server.php
- 热重启htt pserver => php server.php -s reload
- 关闭http server => php server.php -s stop
- 启动某个服务 => app/service user
- 热重启某个服务 => app/service user reload
- 关闭某个服务 => app/service user stop

#### 要注意的点
- 1.因为是异步的，无法设置swoole的max_request参数,stop 与reload的使用也会使部分请求失败。（解决：升级版本到>1.9.17）
- 2.格外内存释放的问题，局部静态变量，全局变量的释放。
- 3.断线重连机制内部已封装(在执行sql时如果出现长连接已失效，将尝试3次重连操作)。
