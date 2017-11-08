### Dao

> ##### 框架内部会做断线重连，失败3次后将进行重连操作

#### 文档参考：[Doctrine DBAL’s documentation](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/index.html)

##### $this-&gt;getDefault\(\) 

> ##### 获取默认服务器连接,返回\Doctrine\DBAL\Connection对象

##### $this-&gt;getRead\($name = null\) 

> ##### 获取读服务器连接，若name不填，随机读取。

##### $this-&gt;getWrite\($name = null\)

> ##### 获取写服务器连接，若name不填，随机读取。

##### $this-&gt;getAllRead\(\)

> 获取所有读服务器的连接

##### $this-&gt;getAllWrite\(\)

> 获取所有写服务器的连接

##### $this-&gt;querySql\($sql, $type, $name = null\)

> \* @param  sql
>
>      \* @param  type\[write\|all\_write\|read\|all\_read\|default\]
>
>      \* @param  name

##### 具体业务中使用：

```

namespace src\Service\User\Dao\Impl;

use Dao;
use src\Service\User\Dao\UserDao;

class UserDaoImpl extends Dao implements UserDao
{
    protected $table = "user";

    public function getUser($id)
    {
        $queryBuilder = $this->getDefault()->createQueryBuilder();
        $queryBuilder
            ->select("*")
            ->from($this->table)
            ->where('id = ?')
            ->setParameter(0, $id);
            
        return $queryBuilder->execute()->fetch();
    }

    public function addUser($user)
    {
        $conn = $this->getDefault();
        $affected = $conn->insert($this->table, $user);
        if ($affected <= 0) {
            return fasle;
        }
        return $conn->lastInsertId();
    }

    public function getUserByMobile($mobile)
    {
        $queryBuilder = $this->getDefault()->createQueryBuilder();
        $queryBuilder
            ->select("*")
            ->from($this->table)
            ->where('mobile = ?')
            ->setParameter(0, $mobile);
            
        return $queryBuilder->execute()->fetch();
    }

    public function updateUserPassword($userId, $password)
    {
        return $this->getDefault()->update($this->table, ['password' => $password], ['id' => $userId]);
    }
}

```



