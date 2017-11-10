### 环境依赖

* [**hiredis**](https://github.com/redis/hiredis)**（redis异步库）**
* **redis**
* **mysql**
* **php &gt;5.6 或者 php &gt; 7.0**
* swoole &gt;=1.9.17\(建议升级到最新版本\) \(在编译swoole时加入--enable-async-redis，开启异步redis客户端, --enable-openssl开启openssl支持,--with-openssl-dir指定你的openssl目录\)

> 注：openssl是用于http异步客户端抓取https网址时依赖的模块，可以选择性开启


#### hiredis安装命令

    wget https://github.com/redis/hiredis/archive/v0.13.3.zip
    unzip v0.13.3.zip
    cd hiredis-0.13.3
    sudo make && sudo make install
    sudo ldconfig

