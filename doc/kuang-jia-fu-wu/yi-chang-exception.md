### 异常Exception

#### 以传统的try，catch抓取异常

> ##### 如果在业务层不catch，框架层会捕捉，并返回一个500的server error响应。
>
> ##### 如果在开发环境会返回一个500的具体错误的trace响应。

```
    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }
```



