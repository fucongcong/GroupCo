<?php

namespace Api\User;

use Api\User\Model\GetUserReq;
use Api\User\Model\GetUserRes;
use Api\User\Model\AddUserReq;
use Api\User\Model\AddUserRes;

interface UserService
{
    public function getUser(GetUserReq $getUserReq) : GetUserRes;

    public function addUser(AddUserReq $addUserReq) : AddUserRes;

    public function getUserByMobile($mobile);

    public function updateUserPassword($userId, $password);
}