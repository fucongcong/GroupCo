### Request

#### 参照symfony2的Request服务

* ##### GET {#get}

```
public function testAction(Request $request)
{
    //get
    $request -> query -> get('xxx');
    $request -> query -> all();
}
```

* ##### POST {#post}

```
public function testAction(Request $request)
{
    //post
    $request -> request -> get('xxx');
    $request -> request -> all()
}
```

* ##### FILE {#file}

```
public function testAction(Request $request)
{
    //file
    $request -> file -> get('xxxx');
}
```



