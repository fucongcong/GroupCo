# Group-Co

[![Build Status](https://travis-ci.org/fucongcong/co-framework.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co) 
[![Code Climate](https://codeclimate.com/github/fucongcong/co-framework/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

### 框架结构

框架其实分为两大板块, 协程客户端(BFF —— Backend For Frontend)与提供基础服务的服务端。

#### 客户端（BFF）
* 利用协程特性以同步方式来编写异步代码，增强可读性。
* 将swoole的异步特性与传统框架的MVC相结合。
* 和nodejs类似，BFF端应该是胶水层(类似传统MVC的控制层)，API提供者

#### 服务端 
* 利用swoole的多进程模式创建，当前版本仅支持RPC调用。

#### 服务化
* 目前实现了以Zookeeper、Redis、Mysql为注册中心的服务化治理.
* 支持了Apollo的配置中心化
* 服务发现，客户端缓存、心跳检测、服务监听

### 如何使用协程客户端，与传统框架的区别？
* 框架基本使用与传统框架基本一致，路由，控制器，调用基础服务
* 在异步调用的地方需要以yield关键词来触发协程切换

### 为什么服务端不采用swoole的4.X版本协程？
* 业务码迁移方便。不使用协程，在原项目或者新项目微服务化时，可以无脑迁移，完全不用担心协程化导致的连接释放、全局变量问题等等诸多限制。
* 多进程模式可以将单连接请求速度优化，利用task机制
* 稳定性、已得到线上验证

### 生产环境使用
* GroupCo框架目前已经全线用于我们团队，日均处理请求百万次，基础服务调用耗时平均约为0.1ms
* 大型项目，服务发现不建议使用redis/mysql。也可以自己集成etcd/consul等其他服务发现工具（框架后面会更新支持）

### 特性

* 全异步协程调度，支持高并发
* 服务发现，客户端缓存、心跳检测、服务监听
* 统一配置中心
* 异步TCP，HTTP客户端
* 异步日志
* 异步文件读写
* 异步Mysql
* 异步Mysql事务处理
* 异步Redis
* 支持Tcp、Mysql、Redis、WebSocket连接池
* SOA服务化调用，内部封装完整的RPC通信，服务端采用异步Task处理后合并数据并返回。
* 异步TCP客户端支持并行、串行调用
* Twig、Doctrine支持视图、服务数据层
* 单元测试覆盖

### 文档总览

* 快速开始
  * [环境依赖](doc/yin-yan/huan-jing-yi-lai.md)
  * [启动项目](doc/yin-yan/qi-dong-xiang-mu.md)
  * [Docker容器启动](doc/yin-yan/docker.md)
* 客户端
  * [异步Tcp客户端](doc/kuang-jia-fu-wu/yi-bu-tcp-ke-hu-duan.md)
  * [异步WebSocket客户端](doc/kuang-jia-fu-wu/yi-bu-websocket-ke-hu-duan.md)
  * [异步Http客户端](doc/kuang-jia-fu-wu/yi-bu-http-ke-hu-duan.md)
  * [异步Redis客户端](doc/kuang-jia-fu-wu/sd.md)
  * [异步Mysql客户端](doc/kuang-jia-fu-wu/yi-bu-mysql-ke-hu-duan.md)
  * [异步Log日志](doc/kuang-jia-fu-wu/yi-bu-log.md)
  * [异步文件读写](doc/kuang-jia-fu-wu/yi-bu-wen-jian-du-xie.md)
  * [异常Exception](doc/kuang-jia-fu-wu/yi-chang-exception.md)
* 服务端\(用于基础服务开发\)
  * [Service](doc/tong-bu-fu-wu/service.md)
  * [Dao](doc/tong-bu-fu-wu/dao.md)
  * [Cache](doc/tong-bu-fu-wu/rediscache.md)
  * [Log日志类](doc/tong-bu-fu-wu/logri-zhi-lei.md)
  * [FileCache文件缓存类](doc/tong-bu-fu-wu/filecachewen-jian-huan-cun-lei.md)
* 服务中心
  * [服务治理流程](doc/fu-wu-zhong-xin/fu-wu-zhi-li-liu-cheng.md)
  * [注册中心](doc/fu-wu-zhong-xin/zhu-ce-zhong-xin.md)
  * [服务调用](doc/fu-wu-zhong-xin/yi-bu-http-server-zhong-shi-yong-fu-wu.md)
  * [使用TCP连接池](doc/fu-wu-zhong-xin/yi-bu-http-server-lian-jie-chi.md)
  * [服务调用监控](doc/fu-wu-zhong-xin/fu-wu-diao-yong-jian-kong.md)
  * [服务调用失败事件](doc/fu-wu-zhong-xin/fu-wu-diao-yong-shi-bai.md)
  * [调试模式](doc/fu-wu-zhong-xin/diao-shi-mo-shi.md)
* 配置中心
  * [配置中心的使用](doc/pei-zhi-zhong-xin/shi-yong.md)
* 框架基础类
  * [Config配置类](doc/ji-chu-fu-wu/config.md)
  * [StaticCache静态缓存类](doc/ji-chu-fu-wu/staticcache.md)
  * [Route路由类](doc/ji-chu-fu-wu/routelu-you-lei.md)
  * [Controller控制器类](doc/ji-chu-fu-wu/controllerkong-zhi-qi-lei.md)
  * [View视图类](doc/ji-chu-fu-wu/viewshi-tu-lei.md)
  * [Request请求类](doc/ji-chu-fu-wu/requestqing-qiu-lei.md)
  * [Response响应类](doc/ji-chu-fu-wu/responsexiang-ying-lei.md)
  * [Event事件类](doc/ji-chu-fu-wu/eventshi-jian-lei.md)
  * [Listener监听类](doc/ji-chu-fu-wu/listenerjian-ting-lei.md)
  * [Subscriber多事件监听](doc/ji-chu-fu-wu/subscriberduo-shi-jian-jian-ting.md)
  * [EventDispatcher事件调度](doc/ji-chu-fu-wu/eventdispatchershi-jian-diao-du-lei.md)
* [单元测试](doc/dan-yuan-ce-shi/dan-yuan-ce-shi.md)
* [控制台](doc/kong-zhi-tai/kong-zhi-tai.md)

### 案例Demo与最佳实践
- [WebSocket简单示例,聊聊集群时的消息转发](doc/demo/ws.md)
- [实现服务异常邮件通知](doc/demo/fu-wu-yi-chang.md)
- [秒杀系统,与GO切磋](https://github.com/fucongcong/GroupCo/tree/co/seckill)
- [日志分析服务](doc/demo/log.md)
- Api服务

### BUG反馈
如果你在使用过程中遇到安全或者框架层面使用bug，请提issue。

### 架构模型
- [架构模型](doc/fu-wu-zhong-xin/jiagou.md)

### 与Go的协程的区别
基于Swoole的异步与php的Generator实现的异步协程，而go语言是内置协程。
