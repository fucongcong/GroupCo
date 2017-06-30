<?php

namespace src\Async\User\Service;

interface UserService
{
	public function getUser($id);

    public function addUser($user);

    public function getUserByMobile($mobile);

    public function updateUserPassword($userId, $password);
}