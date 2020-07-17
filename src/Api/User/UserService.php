<?php

namespace Api\User;

interface UserService
{
    public function getUser(\Api\User\Model\GetUserReq $getUserReq) : \Api\User\Model\GetUserRes;

    public function addUser(\Api\User\Model\AddUserReq $addUserReq) : \Api\User\Model\AddUserRes;

    public function getUserByMobile(\Api\User\Model\GetUserByMobileReq $getUserByMobileReq) : \Api\User\Model\GetUserByMobileRes;

    public function updateUserPassword(\Api\User\Model\UpdateUserPasswordReq $UpdateUserPasswordReq) : \Api\User\Model\UpdateUserPasswordRes;
}