<?php

namespace src\Service\User\Service\Impl;

use src\Service\User\Service\Rely\UserBaseService;
use src\Service\User\Service\UserService;
use Cache;

class UserServiceImpl extends UserBaseService implements UserService
{
	public function getUser($id)
	{
        $user = Cache::get('user_'.$id);
        if (!$user) {
            $user = $this->getUserDao()->getUser($id);
            Cache::set('user_'.$id, $user, 3600);
        }

        if ($user) {
            $user['profile'] = $this->getUserProfileService()->getUserProfile($id);
        }
        
		return $user;
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
        if ($this->getUserByMobile($user['mobile'])) {
            return false;
        }
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