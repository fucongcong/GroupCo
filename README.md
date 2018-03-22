# Group-Co(秒杀案例)

#### 利用异步特性，来抗秒杀环节带来的高并发压力。

#### 秒杀系统设计时，常常遇到的问题（我们不谈前端优化，只关注并发一瞬间，后端接口的处理）
- 并发高，服务压力会突然上升
- 有可能出现库存溢出，如果代码写的不严谨
- 数据层同步问题（这个我们先不考虑）

#### 解决思路
- 在redis连接池资源占满时，拦截一部分请求
- 使用连接池，防止过多的长连接把reids搞挂了
- 使用异步协程特性，解决io密集的问题

#### 测试
- 修改src\Web\Controller\Home\DefaultController的seckillAction的秒杀开始时间
- 访问/seckill/123 后面的数字为用户ID
- 使用压测工具测试并发

#### mac pro 4核8g测试性能与并发数。同样使用redis连接池，最大连接数为50。用GroupCo框架，最后并发为2000qps左右，而用GO语言纯业务下达到了9000qps左右！

#### 测试结果。

    siege -c 800  -f url.txt -i -b -t 1

    Transactions:             114030 hits
    Availability:             100.00 %
    Elapsed time:              59.34 secs
    Data transferred:           1.97 MB
    Response time:              0.40 secs
    Transaction rate:        1921.64 trans/sec
    Throughput:             0.03 MB/sec
    Concurrency:              762.40
    Successful transactions:      114030
    Failed transactions:               0
    Longest transaction:            1.65
    Shortest transaction:           0.00 

#### 与Go比较。我用Go语言写了一版同样的逻辑（seckill.go）

    siege -c 800  -f url-go.txt -i -b -t 1

    Transactions:             559583 hits
    Availability:             100.00 %
    Elapsed time:              59.34 secs
    Data transferred:          16.39 MB
    Response time:              0.02 secs
    Transaction rate:        9430.11 trans/sec
    Throughput:             0.28 MB/sec
    Concurrency:              221.19
    Successful transactions:      559583
    Failed transactions:               0
    Longest transaction:            0.29
    Shortest transaction:           0.00

#### 性能分析
- 同样是协程，php是通过generator进行调度，而go使用底层实现，性能上会存在一定损耗。
- GrouoCo是框架，封装了框架层次的一些业务逻辑，而Go版本是没有的，这里也会有一些性能损耗。
- 语言本身，php是脚本语言，而Go作为编译型语言，在执行速度上上占有优势。
- 从业务层次讲，GroupCo为熟悉php的同学创造了更多尝试处理并发的机会。不需要在去重新学习一门语言。
- 最后，建议大家还是要去了解Go(对于写过脚本语言的同学，Go学起来不会像写java那样费劲)，作者也在学习中~。
