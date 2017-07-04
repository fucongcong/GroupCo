<?php

namespace src\Service\User\Service\Impl;

use src\Service\User\Service\Rely\UserBaseService;
use src\Service\User\Service\UserService;

class UserServiceImpl extends UserBaseService implements UserService
{
	public function getUser($id)
	{  
		return $this->getUserDao()->getUser($id);
	}

    //单进程慢速任务 通过异步的多task去做，速度会翻倍
    public function getUsersCache($ids)
    {   
        //异步多task模式。耗时8ms
        foreach ($ids as $id) {
            $this->task('User\User::getUser', ['id' => $id]);
        }

        return $this->finish();

        //正常模式 耗时250ms
        // $users = [];
        // foreach ($ids as $id) {
        //     $users[] = $this->getUser($id);
        // }

        // return $users;
    }

    public function addUser($user)
    {
        return $this->getUserDao()->addUser($user);
    }

    public function addUsers($users)
    {
        //Transaction
        $connection = app('dao')->getDefault();
        try {
            $connection->beginTransaction();
            foreach ($users as $user) {
                $this->addUser($user);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    public function getUserByMobile($mobile)
    {
        return $this->getUserDao()->getUserByMobile($mobile);
    }

    public function updateUserPassword($userId, $password)
    {
        return $this->getUserDao()->updateUserPassword($userId, $password);
    }
}