### 启动项目\(请先完成环境依赖安装\)

1. #### 克隆项目
2. #### 执行 =&gt; composer install
3. #### 新建一个runtime目录，用于存放日志等cache文件
4. #### 配置config中的database配置文件
5. #### 设置config/service.php中的registry\_address.目前只支持redis、mysql作为注册中心
6. #### 启动http server =&gt; php server.php
7. #### 访问 [http://localhost:9777/](http://localhost:9777/) 开始异步协程之旅



