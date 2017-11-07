# Group-Co

[![Build Status](https://travis-ci.org/fucongcong/co-framework.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co)  
[![Code Climate](https://codeclimate.com/github/fucongcong/co-framework/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

### 为什么写这个框架？

* 利用协程特性以同步方式来编写异步代码，增强可读性。
* 将swoole的异步特性与传统框架的MVC相结合。
* 可以用作api也可以用作http server,rpc server.
* 目前实现了以Redis、Mysql为注册中心的服务化治理.

### 如何使用，与传统框架的区别？

* 框架基本使用与传统框架基本一致，路由，控制器，服务层，数据层。
* 在异步调用的地方需要以yield关键词来触发协程切换

### 特性

* 全异步协程调度，支持高并发
* 异步TCP，HTTP客户端
* 异步日志
* 异步文件读写
* 异步Mysql
* 异步Mysql事务处理
* 异步Redis
* 支持Mysql连接池,Redis连接池
* SOA服务化调用，内部封装完整的RPC通信，服务端采用异步Task处理后合并数据并返回。
* 异步TCP客户端支持并行、串行调用
* 支持EOF结束符协议、自定义网络通信协议，支持json化、php序列化包体，支持gzip。
* Twig、Doctrine支持视图、服务数据层
* 单元测试覆盖

#### 



