#### 前言
WebSocket相信大家在使用时并不陌生，在做IM、消息转发、游戏、直播等等领域都使用广泛。
那么如何在集群环境，有多台WebSocket服务器下进行不同用户间的消息转发呢？

#### 思路
1.首先我们实现api服务器利用WebSocket连接池与我们WebSocket的主服务器进行长连接：

 API服务器 <-> WebSocket集群

2.用户的WebSocket客户端连接到WebSocket服务器，在用户连接到WebSocket集群时，记录下该用户连接的ws信息。包括：哪台WebSocket，他的fd(文件描述符)

 用户客户端 <-> WebSocket集群

3.不同用户之间消息间的转发:

 用户消息 <-> API服务器 <-> WebSocket集群 <-> 用户客户端

用户通过api服务器的中转实现跨WebSocket服务器的消息转发。

#### 优势 
设计优势在于若用户量持续增长，完全可以通过增加WebSocket服务器与API服务器来实现消息的中转

#### 实现Demo
- 向app.php 中的onWorkStartServices添加 Group\Async\Pool\WebSocketPoolServiceProvider
- 启动webscoket服务:  php webscoket.php
- 启动api服务: php server.php
- 打开ws客户端页面: http://localhost:9778/websocket 记得打开web开发者工具
- 实现消息群发、转发,调用api: http://localhost:9778/sendMsg?msg=hello 查看客户端页面返回

#### 具体复杂的业务逻辑，则需要修改api与ws主服务器的代码交互