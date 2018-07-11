#### 网关层
特点：最外层网关需要高性能、支持高并发。

- Go语言实现Gateway，方便定制
- Kong（nginx+lua基于nginx）
- Traefik（Go实现的，性能不如nginx）

#### 服务聚合层
特点：需要快速开发应对外部业务的多变，调用基础服务即可。io较密集，建议使用异步框架。

- GroupCo (php的异步协程框架，php的快速开发与异步协程的支持并发)

#### 基础服务层
特点：基础服务要求稳定，严谨。使用RPC/Http与上层通信

- GroupCo（内置RPC,php开发快）
- [GroupCo-java](https://github.com/fucongcong/GroupCo-java) (java实现的服务层，更严谨，低耦合)

#### 日志分析

- ELK（开箱即用）

#### 配置中心

- GroupCo内置[APOLLO](https://github.com/ctripcorp/apollo)

#### 服务发现

- 基于Zookeeper
- 基于Redis
- 基于Mysql

#### 分布式队列

- 基于Redis
- Kafaka
- Beanstalk
- Rabbitmq
- RocketMQ

#### 分布式任务调度

#### 自动化部署发布

- CI 
- jenkins

#### 流程图
![](/service.png)
