<?php

namespace src\Async\User\Service\Rely;

use Group\Async\Service;

abstract class UserBaseService extends Service
{
    public function getUserDao()
    {
        return $this->createDao("User:User");
    }
}