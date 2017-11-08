### Response

#### 参照symfony2的Response服务

* ##### 常规 {#常规}

```
    public function testAction(Request $request, $id)
    {
        yield new \Response('这是文本');
    }
```

* ##### json格式 {#json格式}

```
    public function testAction(Request $request, $id)
    {
        yield new \JsonResponse('这是文本');
    }
```

* ##### 重定向 {#重定向}

```
    public function testAction(Request $request, $id)
    {
        yield $this->redirect('http://xxxx');
    }
```



