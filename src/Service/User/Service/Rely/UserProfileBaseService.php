<?php

namespace src\Service\User\Service\Rely;

use Group\Sync\Service;

abstract class UserProfileBaseService extends Service
{
    public function getUserService()
    {
        return $this->createService("User:User");
    }
}