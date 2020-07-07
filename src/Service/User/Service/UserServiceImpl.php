<?php

namespace src\Service\User\Service;

use src\Service\User\Service\Rely\UserBaseService;
use Api\User\UserService;
use Api\User\Model\GetUserReq;
use Api\User\Model\GetUserRes;
use Api\User\Model\AddUserReq;
use Api\User\Model\AddUserRes;
use Api\User\Model\User;
use Api\User\Model\UserProfile;
use Cache;

class UserServiceImpl extends UserBaseService implements UserService
{
    public function getUser(GetUserReq $getUserReq) : GetUserRes
    {   
        $id = $getUserReq->getId();

        $user = Cache::get('user_'.$id);
        if (!$user) {
            $user = $this->getUserDao()->getUser($id);
            Cache::set('user_'.$id, $user, 3600);
        }

        $res = new GetUserRes();
        if ($user) {
            $profile = $this->getUserProfileService()->getUserProfile($id);
            $user['profile'] = new UserProfile($profile);

            $res->setUser(new User($user));
        }

        return $res;
    }

    public function addUser(AddUserReq $addUserReq) : AddUserRes
    {   
        $user = $addUserReq->getUser();

        if ($this->getUserByMobile($user->getMobile())) {
            //直接return一个空的对象出去
            return new AddUserRes();
        }

        $uid = $this->getUserDao()->addUser($user);

        $res = new AddUserRes();
        $res->setId($uid);
        return $res;
    }

    public function getUserByMobile($mobile)
    {
        return $this->getUserDao()->getUserByMobile($mobile);
    }

    public function updateUserPassword($userId, $password)
    {
        return $this->getUserDao()->updateUserPassword($userId, $password);
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
}