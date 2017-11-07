### 异步Mysql客户端

#### AsyncMysql::query\($sql, $usePool = true\)

> 第二个参数设为false将不会使用连接池中的资源，默认都会从连接池中取，配置连接池数量 =&gt; config/database.php

##### 具体使用

        use AsyncMysql;

        //设置超时时间
        AsyncMysql::setTimeout(2);

        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) 
        VALUES (NULL, '18768122222', '11111')"));

        //失败返回false   
        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
        }

#### 异步Mysql事务处理

> 与传统事务一样使用，只是需要加上yield关键词，以异步方式调用

        use AsyncMysql;

        public function test()
        {
            try {
                yield AsyncMysql::begin();

                $res = (yield $this->doTrans());
                if ($res === false) {
                    throw new \Exception("need roll back");
                }

                yield AsyncMysql::commit();
            } catch (\Exception $e) {
                yield AsyncMysql::rollback();
            }
        }

        public function doTrans()
        {
            $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) 
            VALUES (NULL, '187681343332', '11111')"));

            if ($res) {
                $result = $res->getResult();
                $affectedRows = $res->getAffectedRows();
                $id = $res->getInsertId();
                $res = (yield AsyncMysql::query("SELECT * FROM `user` WHERE id = {$id}"));
                $res = (yield AsyncMysql::query("SELECT * FROM `user`"));
                $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}", false));
            }

            yield true;
        }



