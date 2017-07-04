<?php

namespace src\Service\User\Service\Rely;

use Group\Sync\Service;

abstract class UserBaseService extends Service
{
    public function getUserDao()
    {
        return $this->createDao("User:User");
    }
}